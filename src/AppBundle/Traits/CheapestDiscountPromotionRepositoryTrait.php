<?php

namespace AppBundle\Traits;

use AppBundle\Entity\Repository\CheapestDiscountPromotionRepository;

/**
 * Implements a setter to inject the 'app.repository.cheapest_discount_promotion' service.
 */
trait CheapestDiscountPromotionRepositoryTrait
{
    /**
     * @var CheapestDiscountPromotionRepository $CheapestDiscountPromotionRepository
     */
    private $CheapestDiscountPromotionRepository;

    /**
     * Get $CheapestDiscountPromotionRepository.
     *
     * @return CheapestDiscountPromotionRepository
     */
    public function getCheapestDiscountPromotionRepository()
    {
        return $this->CheapestDiscountPromotionRepository;
    }

    /**
     * Set $CheapestDiscountPromotionRepository.
     *
     * @param CheapestDiscountPromotionRepository $CheapestDiscountPromotionRepository  $CheapestDiscountPromotionRepository
     *
     * @return self
     */
    public function setCheapestDiscountPromotionRepository(CheapestDiscountPromotionRepository $CheapestDiscountPromotionRepository)
    {
        $this->CheapestDiscountPromotionRepository = $CheapestDiscountPromotionRepository;

        return $this;
    }
}
