<?php

namespace AppBundle\Traits;

use Doctrine\ORM\EntityManager;

trait EntityManagerTrait
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Get the value of entityManager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Set the value of entityManager.
     *
     * @param EntityManager $entityManager
     *
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }
}
