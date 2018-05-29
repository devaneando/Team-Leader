<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product.
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ProductRepository")
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=60, unique=true)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var ArrayCollection|Category[]
     *
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="products")
     */
    protected $categories;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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
     * @return Product
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
     * Set code.
     *
     * @param string $code
     *
     * @return Product
     */
    public function setCode(string $code)
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
     * Set price.
     *
     * @param float $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get the value of categories.
     *
     * @return ArrayCollection|Category[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set the value of categories.
     *
     * @param ArrayCollection|Category[] $categories
     *
     * @return self
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Add a category to the categories collection.
     *
     * @param Category $category
     *
     * @return self
     */
    public function addCategory(Category $category)
    {
        if ($this->categories->contains($category)) {
            return $this;
        }
        $this->categories->add($category);
        $category->addProduct($this);

        return $this;
    }

    /**
     * Remove a category from the categories collection.
     *
     * @param Category $category
     *
     * @return self
     */
    public function removeCategory(Category $category)
    {
        if (!$this->categories->contains($category)) {
            return $this;
        }
        $this->categories->removeElement($category);
        $category->removeProduct($this);

        return $this;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Product
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
