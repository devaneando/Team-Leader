<?php

namespace AppBundle\Entity;

use AppBundle\Entity\CategoryFreebiePromotion;
use AppBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category.
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=120, unique=true)
     */
    private $name;

    /**
     * @var ArrayCollection|Product[]
     *
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="categories", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="categories_products",
     *     joinColumns={
     *         @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *     }
     * )
     */
    private $products;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CategoryFreebiePromotion", mappedBy="category")
     */
    private $categoryFreebiePromotions;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->categoryFreebiePromotions = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of products.
     *
     * @return ArrayCollection|Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set the value of products.
     *
     * @param ArrayCollection|Product[] $products
     *
     * @return self
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * Add a product to the products collection.
     *
     * @param Product $product
     *
     * @return self
     */
    public function addProduct(Product $product)
    {
        if ($this->products->contains($product)) {
            return $this;
        }
        $this->products->add($product);
        $product->addCategory($this);

        return $this;
    }

    /**
     * Remove a product from the products collection.
     *
     * @param Product $product
     *
     * @return self
     */
    public function removeProduct(Product $product)
    {
        if (!$this->products->contains($product)) {
            return $this;
        }
        $this->products->removeElement($product);
        $product->removeCategory($this);

        return $this;
    }

    /**
     * Get the value of categoryFreebiePromotions.
     *
     * @return ArrayCollection
     */
    public function getCategoryFreebiePromotions()
    {
        return $this->categoryFreebiePromotions;
    }

    /**
     * Set the value of categoryFreebiePromotions.
     *
     * @param ArrayCollection $categoryFreebiePromotions
     *
     * @return self
     */
    public function setCategoryFreebiePromotions(ArrayCollection $categoryFreebiePromotions)
    {
        $this->categoryFreebiePromotions = $categoryFreebiePromotions;

        return $this;
    }

    /**
     * Add a CategoryFreebiePromotion to the CategoryFreebiePromotions collection.
     *
     * @param CategoryFreebiePromotion $ategoryFreebiePromotion
     *
     * @return self
     */
    public function addCategoryFreebiePromotion(CategoryFreebiePromotion $ategoryFreebiePromotion)
    {
        if ($this->categoryFreebiePromotions->contains($ategoryFreebiePromotion)) {
            return $this;
        }
        $this->categoryFreebiePromotions->add($ategoryFreebiePromotion);

        return $this;
    }

    /**
     * Remove a CategoryFreebiePromotion from the CategoryFreebiePromotions collection.
     *
     * @param CategoryFreebiePromotion $ategoryFreebiePromotion
     *
     * @return self
     */
    public function removeCategoryFreebiePromotion(CategoryFreebiePromotion $ategoryFreebiePromotion)
    {
        if (!$this->categoryFreebiePromotions->contains($ategoryFreebiePromotion)) {
            return $this;
        }
        $this->categoryFreebiePromotions->removeElement($ategoryFreebiePromotion);

        return $this;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Category
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
