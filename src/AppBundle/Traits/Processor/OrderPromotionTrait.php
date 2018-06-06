<?php

namespace AppBundle\Traits\Processor;

use AppBundle\Processor\OrderPromotion;

/**
 * Implements a setter to inject the 'app.processor.order_promotion' service.
 */
trait OrderPromotionTrait
{
    /** @var OrderPromotion $orderPromotion */
    private $orderPromotion;

    /**
     * Get the value of orderPromotion.
     *
     * @return OrderPromotion
     */
    public function getOrderPromotion()
    {
        return $this->orderPromotion;
    }

    /**
     * Set the value of orderPromotion.
     *
     * @param mixed $orderPromotion
     *
     * @return self
     */
    public function setOrderPromotion($orderPromotion)
    {
        $this->orderPromotion = $orderPromotion;

        return $this;
    }
}
