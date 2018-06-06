<?php

namespace Tests\AppBundle\Processor;

use AppBundle\Model\Order;
use AppBundle\Processor\OrderPromotion;
use AppBundle\Service\OrderService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * vendor/bin/phpunit -c phpunit.xml.dist tests/Processor/OrderPromotionTest.php.
 */
class OrderPromotionTest extends WebTestCase
{
    protected $jsonData = <<<JSON
    {
        "id": "123",
        "customer-id": "900",
        "items": [
          {
            "product-id": "SW003",
            "quantity": "50",
            "unit-price": "3.50",
            "total": "175.00"
          },
          {
            "product-id": "PH009",
            "quantity": "100",
            "unit-price": "12.50",
            "total": "1250.00"
          }
        ],
        "total": "1425.00"
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

    public function testDiscount()
    {
        /** @var OrderPromotion $orderPromotion */
        $orderPromotion = $this->getContainer()->get('app.processor.order_promotion');
        /** @var OrderService $orderService */
        $orderService = $this->getContainer()->get('app.service.order_service');

        $order = $orderService->jsonToOrder($this->jsonData);

        $orderPromotion->setOrder($order);
        $orderPromotion->process();

        $this->assertEquals(10, $order->getDiscount());
        $this->assertEquals(1425, $order->getRawTotal());
        $this->assertEquals(1282.5, $order->getTotal());
        $this->assertEquals(3, $order->getItems()->count());
    }
}
