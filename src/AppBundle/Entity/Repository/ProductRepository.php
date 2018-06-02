<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository.
 */
class ProductRepository extends EntityRepository
{
    /**
     * Find a product by its code.
     *
     * @param string $code The product code
     *
     * @return Product
     */
    public function findOneByCode(string $code)
    {
        /** @var Product $product */
        $product = $this->findOneBy(['code' => $code]);

        return $product;
    }
}
