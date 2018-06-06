<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryFreebiePromotion;
use Doctrine\ORM\EntityRepository;

/**
 * CategoryFreebiePromotionRepository.
 */
class CategoryFreebiePromotionRepository extends EntityRepository
{
    /**
     * Find the categoryFreebiePromotion that gives freebies for less items.
     *
     * @param array $categories
     * @param int $quantity
     *
     * @return array|null
     */
    public function findBestFreebieItems(array $categories, int $quantity)
    {
        $query = $this->_em->createQueryBuilder();
        $query
                ->select('c')
                ->from('AppBundle:CategoryFreebiePromotion', 'c')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('c.enabled', 1),
                        $query->expr()->lte('c.minimumQuantity', ':quantity'),
                        $query->expr()->in('c.category', ':categories')
                    )
                )
                ->distinct()
                ->setParameter('quantity', $quantity)
                ->setParameter('categories', implode(', ', $categories));

        return $query->getQuery()->getResult();
    }
}
