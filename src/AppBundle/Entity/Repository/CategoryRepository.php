<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository.
 */
class CategoryRepository extends EntityRepository
{
    /**
     * Find all enabled categories with the given ids.
     *
     * @param array $ids
     *
     * @return array
     */
    public function findByIds(array $ids)
    {
        $query = $this->_em->createQueryBuilder();
        $query
            ->select('c')
            ->from('AppBundle:Category', 'c')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('c.enabled', 1),
                    $query->expr()->in('c.id', ':ids')
                )
            )
            ->setParameter('ids', $ids);

        return $query->getQuery()->getResult();
    }
}
