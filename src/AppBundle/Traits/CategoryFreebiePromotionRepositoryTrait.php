<?php

namespace AppBundle\Traits;

use AppBundle\Entity\Repository\CategoryFreebiePromotionRepository;

/**
 * Implements a setter to inject the 'app.repository.category_freebie_promotion' service.
 */
trait CategoryFreebiePromotionRepositoryTrait
{
    /**
     * @var CategoryFreebiePromotionRepository $categoryFreebiePromotionRepository
     */
    private $categoryFreebiePromotionRepository;

    /**
     * Get $categoryFreebiePromotionRepository.
     *
     * @return CategoryFreebiePromotionRepository
     */
    public function getCategoryFreebiePromotionRepository()
    {
        return $this->categoryFreebiePromotionRepository;
    }

    /**
     * Set $categoryFreebiePromotionRepository.
     *
     * @param CategoryFreebiePromotionRepository $categoryFreebiePromotionRepository
     *
     * @return self
     */
    public function setCategoryFreebiePromotionRepository(
        CategoryFreebiePromotionRepository $categoryFreebiePromotionRepository
    ) {
        $this->categoryFreebiePromotionRepository = $categoryFreebiePromotionRepository;

        return $this;
    }
}
