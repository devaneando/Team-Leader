<?php

namespace AppBundle\Traits\Processor;

use AppBundle\Processor\CategoryFreebiePromotion;

/**
 * Implements a setter to inject the 'app.processor.category_freebie' service.
 */
trait CategoryFreebiePromotionTrait
{
    /** @var CategoryFreebiePromotion $categoryFreebiePromotion */
    private $categoryFreebiePromotion;

    /**
     * Get the value of categoryFreebiePromotion.
     *
     * @return CategoryFreebiePromotion
     */
    public function getCategoryFreebiePromotion()
    {
        return $this->categoryFreebiePromotion;
    }

    /**
     * Set the value of categoryFreebiePromotion.
     *
     * @param mixed $categoryFreebiePromotion
     *
     * @return self
     */
    public function setCategoryFreebiePromotion($categoryFreebiePromotion)
    {
        $this->categoryFreebiePromotion = $categoryFreebiePromotion;

        return $this;
    }
}
