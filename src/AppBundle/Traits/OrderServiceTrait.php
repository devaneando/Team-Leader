<?php

namespace AppBundle\Traits;

use AppBundle\Service\OrderService;

/**
 * Implements a setter to inject the 'app.service.order_service' service.
 */
trait OrderServiceTrait
{
    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * Get the value of orderService.
     *
     * @return OrderService
     */
    public function getOrderService()
    {
        return $this->orderService;
    }

    /**
     * Set the value of orderService.
     *
     * @param OrderService $orderService
     *
     * @return self
     */
    public function setOrderService(OrderService $orderService)
    {
        $this->orderService = $orderService;

        return $this;
    }
}
