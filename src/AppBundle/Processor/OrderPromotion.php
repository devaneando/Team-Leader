<?php

namespace AppBundle\Processor;

use AppBundle\Exception\Processor\BadOrderException;
use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;
use AppBundle\Processor\ProcessorInterface;
use AppBundle\Traits\OrderPromotionTrait;
use AppBundle\Traits\ProductRepositoryTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Process promotions where a minumum amount is reached and gives a free item or a discount.
 */
class OrderPromotion implements ProcessorInterface
{
    use OrderPromotionTrait;
    use ProductRepositoryTrait;

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
        $discount = $this->getOrderPromotionRepository()->findBestDiscount(
            $this->getOrder()->getRawTotal()
        );

        if ($discount) {
            $this->order->setDiscount($discount->getDiscount());
        }

        $freebie = $this->getOrderPromotionRepository()->findBestFreebie(
            $this->getOrder()->getRawTotal()
        );

        if ($freebie) {
            $product = $this->getProductRepository()->findOneByCode(
                $freebie->getFreebieItem()->getCode()
            );

            $categories = [];
            foreach ($product->getCategories() as $category) {
                $categories[] = $category->getId();
            }

            $orderItem = new OrderItem();
            $orderItem
                ->setCategoryIds($categories)
                ->setPrice(0)
                ->setProductId($product->getId())
                ->setRawQuantity($freebie->getFreebieQuantity());

            $this->order->addItem($orderItem);
        }
    }
}
