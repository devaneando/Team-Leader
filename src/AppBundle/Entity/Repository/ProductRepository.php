<?php

namespace AppBundle\Entity\Repository;

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
        return $this->findOneBy(['code' => $code]);
    }
}
