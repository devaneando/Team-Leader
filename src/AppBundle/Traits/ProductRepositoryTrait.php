<?php

namespace AppBundle\Traits;

use AppBundle\Entity\Repository\ProductRepository;

/**
 * Implements a setter to inject the 'app.repositoy.product' service.
 */
trait ProductRepositoryTrait
{
    /**
     * @var ProductRepository $productRepository
     */
    private $productRepository;

    /**
     * Get $productRepository.
     *
     * @return ProductRepository
     */
    public function getProductRepository()
    {
        return $this->productRepository;
    }

    /**
     * Set $productRepository.
     *
     * @param ProductRepository $productRepository  $productRepository
     *
     * @return self
     */
    public function setProductRepository(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;

        return $this;
    }
}
