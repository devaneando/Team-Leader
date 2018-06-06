<?php

namespace AppBundle\Processor;

use AppBundle\Entity\CategoryFreebiePromotion as CategoryFreebiePromotionEntity;
use AppBundle\Exception\Processor\BadOrderException;
use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;
use AppBundle\Processor\ProcessorInterface;
use AppBundle\Traits\CategoryFreebiePromotionRepositoryTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Process promotions and give free items if a minimum was bought.
 */
class CategoryFreebiePromotion implements ProcessorInterface
{
    const UNEXISTENT_VERY_BIG_NUMBER = 999999999999;

    use CategoryFreebiePromotionRepositoryTrait;

    /**
     * @var Order
     */
    private $order;

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        if (!$this->order) {
            throw new BadOrderException();
        }

        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        foreach ($this->order->getItems() as $item) {
            /** @var OrderItem $item */
            if (!$item->getCategoryIds()) {
                continue;
            }
            $item->setOffer($this->getOffer($item));
        }
    }

    /**
     * Find the best freebie for an OrderItem.
     *
     * @param OrderItem $item
     *
     * @return CategoryFreebiePromotionEntity|null
     */
    protected function findBestOffer(OrderItem $item)
    {
        $promotions = $this->getCategoryFreebiePromotionRepository()->findBestFreebieItems(
                $item->getCategoryIds(),
                $item->getRawQuantity()
            );

        $bestPromotion = ['object' => null, 'minQuantity' => self::UNEXISTENT_VERY_BIG_NUMBER];
        foreach ($promotions as $promotion) {
            /** @var CategoryFreebiePromotionEntity $promotion */
            if ($promotion->getMinimumQuantity() < $bestPromotion['minQuantity']) {
                $bestPromotion['object'] = $promotion;
                $bestPromotion['minQuantity'] = $promotion->getMinimumQuantity();
            }
        }
        if (self::UNEXISTENT_VERY_BIG_NUMBER == $bestPromotion['minQuantity']) {
            return null;
        }

        return $bestPromotion['object'];
    }

    /**
     * Gets the best offer quantity.
     *
     * @param OrderItem $item
     *
     * @return int
     */
    public function getOffer(OrderItem &$item)
    {
        $promotion = $this->findBestOffer($item);
        if (!$promotion) {
            return 0;
        }

        return floor($item->getRawQuantity() / $promotion->getMinimumQuantity());
    }
}
