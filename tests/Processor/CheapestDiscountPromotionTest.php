<?php

namespace Tests\AppBundle\Processor;

use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;
use AppBundle\Processor\CheapestDiscountPromotion;
use AppBundle\Service\OrderService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * vendor/bin/phpunit -c phpunit.xml.dist tests/Processor/CheapestDiscountPromotionTest.php.
 */
class CheapestDiscountPromotionTest extends WebTestCase
{
    protected $jsonData = <<<JSON
    {
        "id": "123",
        "customer-id": "900",
        "items": [
          {
            "product-id": "TO001",
            "quantity": "3",
            "unit-price": "5",
            "total": "15.00"
          },
          {
            "product-id": "PH007",
            "quantity": "10",
            "unit-price": "0.50",
            "total": "5.00"
          }
        ],
        "total": "20.00"
      }
JSON;

    protected static $application;
    protected $container;

    protected function setUp()
    {
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create --if-not-exists');
        self::runCommand('doctrine:schema:update --force --no-interaction');
        self::runCommand('doctrine:fixtures:load --no-interaction');
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet  --env=test', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    protected function getContainer()
    {
        if (null === $this->container) {
            self::bootKernel();
            $this->container = self::$kernel->getContainer();
        }

        return $this->container;
    }

    public function testFreebie()
    {
        /** @var CheapestDiscountPromotion $cheapestDiscountPromotion */
        $cheapestDiscountPromotion = $this->getContainer()->get('app.processor.cheapest_discount');

        /** @var OrderService $orderService */
        $orderService = $this->getContainer()->get('app.service.order_service');

        $order = $orderService->jsonToOrder($this->jsonData);

        $cheapestDiscountPromotion->setOrder($order);
        $cheapestDiscountPromotion->process();
        /** @var OrderItem $item */
        $item = $order->getItems()[2];

        $this->assertEquals(5, $item->getRawTotal());
        $this->assertEquals(4, $item->getTotal());
    }
}
