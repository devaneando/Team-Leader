<?php

namespace AppBundle\Processor;

use AppBundle\Exception\Processor\BadOrderException;
use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;

/**
 * Describes the required methods for a promotion processor.
 */
interface ProcessorInterface
{
    /**
     * Get the value of order.
     *
     * @throws BadOrderException
     *
     * @return Order
     */
    public function getOrder();

    /**
     * Set the value of order.
     *
     * @param Order $order
     *
     * @return self
     */
    public function setOrder(Order $order);

    /**
     * Process an order, applying all the implemented promotions.
     */
    public function process();
}
