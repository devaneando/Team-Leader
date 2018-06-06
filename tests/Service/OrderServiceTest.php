<?php

namespace Tests\AppBundle\Service;

use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;
use AppBundle\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * vendor/bin/phpunit -c phpunit.xml.dist tests/Service/OrderServiceTest.php.
 */
class OrderServiceTest extends KernelTestCase
{
    private $container;

    protected $jsonData = <<<JSON
        {
        "id": "1",
        "customer-id": "2",
        "items": [
            {
            "product-id": "A101",
            "quantity": "10",
            "unit-price": "1.75",
            "total": "17.50"
            },
            {
            "product-id": "A102",
            "quantity": "20",
            "unit-price": "11.75",
            "total": "235"
            },
            {
            "product-id": "A103",
            "quantity": "30",
            "unit-price": "21.75",
            "total": "652,50"
            }
        ],
        "total": "905"
        }
JSON;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
    }

    public function testJsonToOrder()
    {
        $orderService = new OrderService();
        $orderService->setProductRepository($this->container->get('app.repository.product'));

        /** @var Order $order */
        $order = $orderService->jsonToOrder($this->jsonData);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(1, $order->getOrderId());
        foreach ($order->getItems() as $item) {
            /** @var OrderItem $item */
            $this->assertInstanceOf(OrderItem::class, $item);
            $this->assertRegexp('/^A10.*/', $item->getProductId());
        }
    }

    public function testOrderToJson()
    {
        $orderService = new OrderService();

        $order = new Order();
        $order
            ->setCustomerId('10000')
            ->setDiscount(35)
            ->setOrderId('15000');

        $orderItem = new OrderItem();
        $orderItem
            ->setDiscount(15)
            ->setOffer(3)
            ->setPrice(15)
            ->setProductId('999999')
            ->setPromotions(['WEK207']);
        $order->addItem($orderItem);

        $result = '{"id":"15000","customer-id":"10000","total":0,"items":[{"product-id":"999999","quantity":null,'
            .'"unit-price":15,"total":0}]}';
        $this->assertEquals($result, $orderService->orderToJson($order));
    }
}
