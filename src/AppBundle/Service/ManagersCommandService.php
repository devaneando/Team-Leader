<?php

namespace AppBundle\Service;

use AppBundle\Entity\Category;
use AppBundle\Entity\OrderPromotion;
use AppBundle\Entity\Product;
use AppBundle\Traits\CategoryRepositoryTrait;
use AppBundle\Traits\OrderPromotionTrait;
use AppBundle\Traits\ProductRepositoryTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Implements methods common to commands aimed to manage orders, products and categories.
 */
class ManagersCommandService
{
    use CategoryRepositoryTrait;
    use ProductRepositoryTrait;
    use OrderPromotionTrait;

    /**
     * Returns all the existent categories ready to be printed in the console.
     *
     * @return array
     */
    public function listCategory()
    {
        $output = [];
        $categories = $this->getCategoryRepository()->findBy([], ['name' => 'ASC']);
        $output[] = sprintf('<comment>%-10s|%-10s|%s</comment>', 'Id', 'Enabled', 'Category');
        foreach ($categories as $category) {
            /** @var Category $category */
            $output[] = sprintf(
                '<info>%-10s|%-10s|%s</info>',
                $category->getId(),
                $category->getEnabled(),
                $category->getName()
            );
        }

        return $output;
    }

    /**
     * Returns all the existent products ready to be printed in the console.
     *
     * @return array
     */
    public function listProduct()
    {
        $output = [];
        $products = $this->getProductRepository()->findBy([], ['name' => 'ASC']);
        foreach ($products as $product) {
            $lineCategories = [];
            /** @var Product $product */
            foreach ($product->getCategories() as $productCategory) {
                /** @var Category $productCategory */
                $lineCategories[] = sprintf(
                    '- %s: %s',
                    $productCategory->getId(),
                    $productCategory->getName()
                );
            }

            $output[] = sprintf(
                '<comment>%-15s|%-10s|%-15s|%s</comment>',
                'Id',
                'Enabled',
                'Code',
                'Name'
            );
            $output[] = sprintf(
                    '<info>%-15s|%-10s|%-15s|%s</info>',
                    $product->getId(),
                    $product->getEnabled(),
                    $product->getCode(),
                    $product->getName()
            );

            $output[] = '<comment>Category</comment>';
            foreach ($lineCategories as $lineCategory) {
                $output[] = $lineCategory;
            }
            $output[] = '';
        }

        return $output;
    }

    /**
     * Returns all the existent order promotions ready to be printed in the console.
     *
     * @return array
     */
    public function listOrderPromotions()
    {
        $output = [];
        $output[] = sprintf(
            '<comment>%-10s|%-10s|%-10s|%-12s|%-10s|%-15s|%s</comment>',
            'Id',
            'Enabled',
            'Code',
            'Min. Amount',
            'Discount',
            'Freebie',
            'Description'
        );

        $orderPromotions = $this->getOrderPromotionRepository()->findBy(
            [],
            ['discount' => 'ASC', 'code' => 'ASC']
        );

        foreach ($orderPromotions as $orderPromotion) {
            /** @var OrderPromotion $orderPromotion */
            $freebie = 0;
            if ($orderPromotion->getFreebieQuantity()) {
                $freebie = $orderPromotion->getFreebieQuantity().' x obj #'.$orderPromotion->getFreebieItem();
            }

            $output[] = sprintf(
                '<info>%-10s|%-10s|%-10s|%-12s|%-10s|%-15s|%s</info>',
                $orderPromotion->getId(),
                $orderPromotion->getEnabled(),
                $orderPromotion->getCode(),
                $orderPromotion->getMinimumAmount(),
                $orderPromotion->getDiscount(),
                $freebie,
                $orderPromotion->getDescription()
            );
        }

        return $output;
    }
}
