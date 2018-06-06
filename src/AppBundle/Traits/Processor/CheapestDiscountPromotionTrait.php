<?php

namespace AppBundle\Traits\Processor;

use AppBundle\Processor\CheapestDiscountPromotion;

/**
 * Implements a setter to inject the 'app.processor.cheapest_discount' service.
 */
trait CheapestDiscountPromotionTrait
{
    /** @var CheapestDiscountPromotion $cheapestDiscountPromotion */
    private $cheapestDiscountPromotion;

    /**
     * Get the value of cheapestDiscountPromotion.
     *
     * @return CheapestDiscountPromotion
     */
    public function getCheapestDiscountPromotion()
    {
        return $this->cheapestDiscountPromotion;
    }

    /**
     * Set the value of cheapestDiscountPromotion.
     *
     * @param mixed $cheapestDiscountPromotion
     *
     * @return self
     */
    public function setCheapestDiscountPromotion($cheapestDiscountPromotion)
    {
        $this->cheapestDiscountPromotion = $cheapestDiscountPromotion;

        return $this;
    }
}
