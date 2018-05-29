<?php

namespace AppBundle\Traits;

use AppBundle\Entity\Repository\CategoryRepository;

/**
 * Implements a setter to inject the 'app.repositoy.category' service.
 */
trait CategoryRepositoryTrait
{
    /**
     * @var CategoryRepository $categoryRepository
     */
    private $categoryRepository;

    /**
     * Get $categoryRepository.
     *
     * @return CategoryRepository
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * Set $categoryRepository.
     *
     * @param CategoryRepository $categoryRepository  $categoryRepository
     *
     * @return self
     */
    public function setCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;

        return $this;
    }
}
