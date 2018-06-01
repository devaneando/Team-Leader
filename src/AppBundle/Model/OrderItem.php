<?php

namespace AppBundle\Model;

/**
 * Represents an order item.
 */
class OrderItem
{
    /**
     * @var string $productId The productId
     */
    private $productId;

    /**
     * @var int $rawQuantity How many products the customer ordered, without any 'freebies'
     */
    private $rawQuantity;

    /**
     * @var float $price The unitary price of the product
     */
    private $price;

    /**
     * @var float $rawTotal A calculated field with the result of the 'price' times the 'rawQuantity'
     * */
    private $rawTotal;

    /**
     * @var float The final discount given by the processors to the product
     */
    private $discount;

    /**
     * @var float A calculated field with the result of 'rawTotal' * ('discount' / 100 +1)
     */
    private $total;

    /**
     * @var int $offer Any freebies given by the processors to the product
     */
    private $offer;

    /**
     * @var array $categoryIds The ids of the categories the orderItem belongs to
     */
    private $categoryIds;

    /**
     * @var int $quantity A calculated field with the result 'rawQuantity' + 'offer'
     */
    private $quantity;

    /**
     * @var array $promotions The ids of the promotion given by the processors to the product
     */
    private $promotions;

    public function __construct()
    {
        $this->price = 0;
        $this->rawQuantity = 0;
        $this->rawTotal = 0;
        $this->discount = 0;
    }

    /**
     * Get $productId The productId.
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set $productId The productId.
     *
     * @param string $productId  $productId The productId
     *
     * @return self
     */
    public function setProductId(string $productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get $rawQuantity How many products the customer ordered, without any 'freebies'.
     *
     * @return int
     */
    public function getRawQuantity()
    {
        return $this->rawQuantity;
    }

    /**
     * Set $rawQuantity How many products the customer ordered, without any 'freebies'.
     *
     * @param int $rawQuantity $rawQuantity How many products the customer ordered, without any 'freebies'
     *
     * @return self
     */
    public function setRawQuantity(int $rawQuantity)
    {
        $this->rawQuantity = $rawQuantity;
        $this->updateTotals();

        return $this;
    }

    /**
     * Get $price The unitary price of the product.
     *
     * @return float
     */
    public function getPrice()
    {
        return round($this->price, 2);
    }

    /**
     * Set $price The unitary price of the product.
     *
     * @param float $price  $price The unitary price of the product
     *
     * @return self
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
        $this->updateTotals();

        return $this;
    }

    /**
     * Get $rawTotal A calculated field with the result of the 'price' times the 'rawQuantity'.
     *
     * @return float
     */
    public function getRawTotal()
    {
        return round($this->rawTotal, 2);
    }

    /**
     * Set $rawTotal A calculated field with the result of the 'price' times the 'rawQuantity'.
     *
     * @return self
     */
    protected function setRawTotal()
    {
        $this->rawTotal = $this->price * $this->rawQuantity;

        return $this;
    }

    /**
     * Get the final discount given by the processors to the product.
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set the final discount given by the processors to the product.
     *
     * @param float $discount  The final discount given by the processors to the product
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
     * Get a calculated field with the result of 'rawTotal' * ('discount' / 100 +1).
     *
     * @return float
     */
    public function getTotal()
    {
        return round($this->total, 2);
    }

    /**
     * Set a calculated field with the result of 'rawTotal' * ('discount' / 100 +1).
     *
     * @return self
     */
    public function setTotal()
    {
        $this->total = $this->rawTotal * (($this->discount / 100) + 1);

        return $this;
    }

    /**
     * Get $offer Any freebies given by the processors to the product.
     *
     * @return int
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * Set $offer Any freebies given by the processors to the product.
     *
     * @param int $offer  $offer Any freebies given by the processors to the product
     *
     * @return self
     */
    public function setOffer(int $offer)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Get $categoryIds The ids of the categories the orderItem belongs to.
     *
     * @return array
     */
    public function getCategoryIds()
    {
        return $this->categoryIds;
    }

    /**
     * Set $categoryIds The ids of the categories the orderItem belongs to.
     *
     * @param array $categoryIds  $categoryIds The ids of the categories the orderItem belongs to
     *
     * @return self
     */
    public function setCategoryIds(array $categoryIds)
    {
        $this->categoryIds = $categoryIds;

        return $this;
    }

    /**
     * Get $quantity A calculated field with the result 'rawQuantity' + 'offer'.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set $quantity A calculated field with the result 'rawQuantity' + 'offer'.
     *
     * @param int $quantity  $quantity A calculated field with the result 'rawQuantity' + 'offer'
     *
     * @return self
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get $promotions The ids of the promotion given by the processors to the product.
     *
     * @return array
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * Set $promotions The ids of the promotion given by the processors to the product.
     *
     * @param array $promotions  $promotions The ids of the promotion given by the processors to the product
     *
     * @return self
     */
    public function setPromotions(array $promotions)
    {
        $this->promotions = $promotions;

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
}
