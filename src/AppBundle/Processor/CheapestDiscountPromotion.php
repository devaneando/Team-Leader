<?php

namespace AppBundle\Processor;

use AppBundle\Entity\CheapestDiscountPromotion as CheapestDiscountPromotionEntity;
use AppBundle\Exception\Processor\BadOrderException;
use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;
use AppBundle\Processor\ProcessorInterface;
use AppBundle\Traits\CheapestDiscountPromotionRepositoryTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Process promotions and give a discount to the cheapest product.
 */
class CheapestDiscountPromotion implements ProcessorInterface
{
    const UNEXISTENT_VERY_BIG_NUMBER = 999999999999;

    use CheapestDiscountPromotionRepositoryTrait;

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
        $betterDiscount = 0;
        foreach ($this->order->getItems() as $item) {
            /** @var OrderItem $item */
            if (!$item->getCategoryIds()) {
                continue;
            }
            $discount = $this->getOffer($item);
            if ($betterDiscount < $discount) {
                $betterDiscount = $discount;
            }
        }

        $cheapest = $this->order->getCheapestItem();
        $this->order->getItems()->removeElement($cheapest);
        $cheapest->setDiscount($betterDiscount);
        $this->order->addItem($cheapest);
    }

    /**
     * Find the best offer for an OrderItem.
     *
     * @param OrderItem $item
     *
     * @return CheapestDiscountPromotionEntity|null
     */
    protected function findBestOffer(OrderItem $item)
    {
        $promotions = $this->getCheapestDiscountPromotionRepository()->findBestDiscount(
                $item->getCategoryIds(),
                $item->getRawQuantity()
            );

        $bestPromotion = ['object' => null, 'minQuantity' => self::UNEXISTENT_VERY_BIG_NUMBER];
        foreach ($promotions as $promotion) {
            /** @var CheapestDiscountPromotion $promotion */
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
     * Gets the best offer discount.
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

        return $promotion->getDiscount();
    }
}
