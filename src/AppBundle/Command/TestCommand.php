<?php

namespace AppBundle\Command;

use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;
use AppBundle\Serializer\Normalizer\OrderNormalizer;
use AppBundle\Service\OrderService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TestCommand extends ContainerAwareCommand
{
    protected $jsonData = <<<JSON
        {
        "id": "3",
        "customer-id": "5",
        "items": [
            {
            "product-id": "A101",
            "quantity": "2",
            "unit-price": "9.75",
            "total": "19.50"
            },
            {
            "product-id": "A102",
            "quantity": "1",
            "unit-price": "49.50",
            "total": "49.50"
            }
        ],
        "total": "69.00"
        }
JSON;

    protected function configure()
    {
        $this
            ->setName('app:test')
            ->setDescription('Do tests.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var OrderService $orderService */
        $orderService = $this->getContainer()->get('app.service.OrderService');

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
        var_dump($orderService->orderToJson($order));
    }
}
