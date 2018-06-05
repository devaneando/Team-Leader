<?php

namespace AppBundle\Traits;

use AppBundle\Entity\Repository\OrderPromotionRepository;

/**
 * Implements a setter to inject the 'app.repository.order_promotion' service.
 */
trait OrderPromotionTrait
{
    /**
     * @var OrderPromotionRepository $orderPromotionRepository
     */
    private $orderPromotionRepository;

    /**
     * Get $orderPromotionRepository.
     *
     * @return OrderPromotionRepository
     */
    public function getOrderPromotionRepository()
    {
        return $this->orderPromotionRepository;
    }

    /**
     * Set $orderPromotionRepository.
     *
     * @param OrderPromotionRepository $orderPromotionRepository  $orderPromotionRepository
     *
     * @return self
     */
    public function setOrderPromotionRepository(OrderPromotionRepository $orderPromotionRepository)
    {
        $this->orderPromotionRepository = $orderPromotionRepository;

        return $this;
    }
}
