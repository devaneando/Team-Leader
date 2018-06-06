<?php

namespace AppBundle\Model;

use AppBundle\Exception\Command\InvalidParameterException;
use AppBundle\Model\OrderItem;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents an order.
 */
class Order
{
    const UNEXISTENT_VERY_BIG_NUMBER = 999999999999;

    /**
     * @var string $productId The orderId
     */
    private $orderId;

    /**
     * @var string $productId The customerId
     */
    private $customerId;

    /**
     * @var ArrayCollection $items The order items
     */
    private $items;

    /**
     * @var float $rawTotal A calculated field with the sum of the cost of all products
     * */
    private $rawTotal;

    /**
     * @var float The final discount given by the processors to the order
     */
    private $discount;

    /**
     * @var float $rawTotal A calculated field with the result of 'rawTotal' * ('discount' / 100 +1)
     * */
    private $total;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * Get $productId The orderId.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set $productId The orderId.
     *
     * @param string $orderId  $productId The orderId
     *
     * @return self
     */
    public function setOrderId(string $orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get $productId The customerId.
     *
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set $productId The customerId.
     *
     * @param string $customerId  $productId The customerId
     *
     * @return self
     */
    public function setCustomerId(string $customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get $items The order items.
     *
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
        $this->updateTotals();
    }

    /**
     * Set $items The order items.
     *
     * @param ArrayCollection $items  $items The order items
     *
     * @return self
     */
    public function setItems(ArrayCollection $items)
    {
        $this->items = $items;
        $this->updateTotals();

        return $this;
    }

    /**
     * Add an item to the items collection.
     *
     * @param OrderItem $item
     *
     * @return self
     */
    public function addItem(OrderItem $item)
    {
        if ($this->items->contains($item)) {
            return $this;
        }
        $this->items->add($item);
        $this->updateTotals();

        return $this;
    }

    /**
     * Remove an item from the items collection.
     *
     * @param OrderItem $item
     *
     * @return self
     */
    public function removeItem(OrderItem $item)
    {
        if (!$this->items->contains($item)) {
            return $this;
        }
        $this->items->removeElement($item);
        $this->updateTotals();

        return $this;
    }

    /**
     * Get $rawTotal A calculated field with the sum of the cost of all products.
     *
     * @return float
     */
    public function getRawTotal()
    {
        return round($this->rawTotal, 2);
    }

    /**
     * Set $rawTotal A calculated field with the sum of the cost of all products.
     *
     * @return self
     */
    public function setRawTotal()
    {
        $rawTotal = 0;
        foreach ($this->items as $item) {
            /** @var OrderItem $item */
            $rawTotal += $item->getTotal();
        }
        $this->rawTotal = $rawTotal;

        return $this;
    }

    /**
     * Get the final discount given by the processors to the order.
     *
     * @return float
     */
    public function getDiscount()
    {
        return round($this->discount, 2);
    }

    /**
     * Set the final discount given by the processors to the order.
     *
     * @param float $discount  The final discount given by the processors to the order
     *
     * @return self
     */
    public function setDiscount(float $discount)
    {
        $this->discount = $discount;
        $this->updateTotals();

        return $this;
    }

    /**
     * Get $rawTotal A calculated field with the result of 'rawTotal' ('discount' / 100 +1).
     *
     * @return float
     */
    public function getTotal()
    {
        return round($this->total, 2);
    }

    /**
     * Set $rawTotal A calculated field with the result of 'rawTotal' ('discount' / 100 +1).
     *
     * @return self
     */
    public function setTotal()
    {
        $this->total = $this->rawTotal;

        if ($this->discount > 0) {
            $this->total = $this->rawTotal * (1 - ($this->discount / 100));
        }

        return $this;
    }

    /**
     * Update all calculated total fields.
     *
     * @return self
     */
    public function updateTotals()
    {
        $this->setRawTotal();
        $this->setTotal();

        return $this;
    }

    /**
     * Returns the cheapest item in the order.
     *
     * @return OrderItem
     */
    public function getCheapestItem()
    {
        $cheapest = ['object' => null, 'price' => self::UNEXISTENT_VERY_BIG_NUMBER];
        foreach ($this->getItems() as $item) {
            /** @var OrderItem $item */
            if ($item->getPrice() < $cheapest['price']) {
                $cheapest['price'] = $item->getPrice();
                $cheapest['object'] = $item;
            }
        }

        return $cheapest['object'];
    }
}
