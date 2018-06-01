<?php

namespace AppBundle\Service;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;
use AppBundle\Serializer\Normalizer\OrderNormalizer;
use AppBundle\Traits\ProductRepositoryTrait;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Imports and exports an order object.
 */
class OrderService
{
    const SERIALIZATION_GROUP = 'Order_Export';

    use ProductRepositoryTrait;

    /**
     * Unserialize a json string to an order object.
     *
     * @param string $json
     *
     * @return Order
     */
    public function jsonToOrder(string $json)
    {
        $serializer = new Serializer(
            [new OrderNormalizer()],
            ['json' => new JsonEncoder()]
        );

        /** @var Order $order */
        $order = $serializer->denormalize($json, 'json');
        /** @var OrderItem $item */
        foreach ($order->getItems() as $item) {
            /** @var Product $product */
            $product = $this->getProductRepository()->findOneByCode($item->getProductId());
            if ($product) {
                $categories = [];
                /** @var Category $category */
                foreach ($product->getCategories() as $category) {
                    $categories[] = $category->getId();
                }
                $item->setCategoryIds($categories);
            }
        }

        return $order;
    }

    /**
     * Serialize an order object into a json string.
     *
     * @param Order $order
     *
     * @return string
     */
    public function orderToJson(Order $order)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $serializer = new Serializer(
            [new OrderNormalizer()],
            ['json' => new JsonEncoder()]
        );

        return $serializer->serialize($order, 'json');
    }
}
