<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Product;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrderPromotion.
 *
 * @ORM\Table(name="order_promotion")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\OrderPromotionRepository")
 */
class OrderPromotion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=30, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="minimum_amount", type="float")
     */
    private $minimumAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable=true)
     */
    private $discount;

    /**
     * @var int
     *
     * @ORM\Column(name="freebie_quantity", type="integer")
     */
    private $freebieQuantity;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="promotions", cascade={"persist"})
     */

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="promotions")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $freebieItem;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    public function __construct()
    {
        $this->discount = 0;
        $this->freebieQuantity = 0;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return OrderPromotion
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return OrderPromotion
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set minimumAmount.
     *
     * @param float $minimumAmount
     *
     * @return OrderPromotion
     */
    public function setMinimumAmount($minimumAmount)
    {
        $this->minimumAmount = $minimumAmount;

        return $this;
    }

    /**
     * Get minimumAmount.
     *
     * @return float
     */
    public function getMinimumAmount()
    {
        return $this->minimumAmount;
    }

    /**
     * Set discount.
     *
     * @param float $discount
     *
     * @return OrderPromotion
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount.
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set freebieQuantity.
     *
     * @param int $freebieQuantity
     *
     * @return OrderPromotion
     */
    public function setFreebieQuantity($freebieQuantity)
    {
        $this->freebieQuantity = $freebieQuantity;

        return $this;
    }

    /**
     * Get freebieQuantity.
     *
     * @return int
     */
    public function getFreebieQuantity()
    {
        return $this->freebieQuantity;
    }

    /**
     * Get the value of freebieItem.
     *
     * @return Product
     */
    public function getFreebieItem()
    {
        return $this->freebieItem;
    }

    /**
     * Set the value of freebieItem.
     *
     * @var Product $freebieItem
     *
     * @return self
     */
    public function setFreebieItem(Product $freebieItem)
    {
        $this->freebieItem = $freebieItem;

        return $this;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return OrderPromotion
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}
