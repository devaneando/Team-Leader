<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OrderNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Order;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param Order $object  Object to normalize
     * @param string $format  Format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @throws InvalidArgumentException   Occurs when the object given is not an attempted type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     *
     * @return array|string|int|float|bool
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = [
            'id' => $object->getOrderId(),
            'customer-id' => $object->getCustomerId(),
            'total' => $object->getTotal(),
        ];

        $items = [];
        foreach ($object->getItems() as $item) {
            /** @var OrderItem $item */
            $items[] = [
                'product-id' => $item->getProductId(),
                'quantity' => $item->getQuantity(),
                'unit-price' => $item->getPrice(),
                'total' => $item->getTotal(),
            ];
        }
        $data['items'] = $items;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        return 'json' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $data = json_decode($data, true);

        $order = new Order();
        if (isset($data['id'])) {
            $order->setOrderId($data['id']);
        }
        if (isset($data['customer-id'])) {
            $order->setCustomerId($data['customer-id']);
        }

        foreach ($data['items'] as $dataItem) {
            $item = new OrderItem();
            if (isset($dataItem['product-id'])) {
                $item->setProductId($dataItem['product-id']);
            }
            if (isset($dataItem['quantity'])) {
                $item->setRawQuantity($dataItem['quantity']);
            }
            if (isset($dataItem['unit-price'])) {
                $item->setPrice($dataItem['unit-price']);
            }
            $order->addItem($item);
        }

        return $order;
    }
}
