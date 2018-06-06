<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\OrderPromotion;
use Doctrine\ORM\EntityRepository;

/**
 * OrderPromotionRepository
 *.
 */
class OrderPromotionRepository extends EntityRepository
{
    /**
     * Find the orderPromotion with the higher discount for a given amount.
     *
     * @param int $amount
     *
     * @return OrderPromotion|null
     */
    public function findBestDiscount(int $amount)
    {
        $query = $this->_em->createQueryBuilder();
        $query
            ->select('o')
            ->from('AppBundle:OrderPromotion', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('o.enabled', 1),
                    $query->expr()->gt('o.discount', 0),
                    $query->expr()->lte('o.minimumAmount', ':amount')
                )
            )
            ->orderBy('o.discount', 'DESC')
            ->setParameter('amount', $amount);

        $discounts = $query->getQuery()->getResult();
        if ($discounts) {
            return $discounts[0];
        }

        return null;
    }

    /**
     * Find the orderPromotion wich gives the more free products.
     *
     * @param int $amount
     *
     * @return OrderPromotion|null
     */
    public function findBestFreebie(int $amount)
    {
        $query = $this->_em->createQueryBuilder();
        $query
            ->select('o')
            ->from('AppBundle:OrderPromotion', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('o.enabled', 1),
                    $query->expr()->gt('o.freebieQuantity', 0),
                    $query->expr()->lte('o.minimumAmount', ':amount')
                )
            )
            ->orderBy('o.freebieQuantity', 'DESC')
            ->setParameter('amount', $amount);

        $freebies = $query->getQuery()->getResult();
        if ($freebies) {
            return $freebies[0];
        }

        return null;
    }
}
