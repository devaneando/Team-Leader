<?php

namespace Tests\AppBundle\Processor;

use AppBundle\Model\Order;
use AppBundle\Model\OrderItem;
use AppBundle\Processor\CategoryFreebiePromotion;
use AppBundle\Service\OrderService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * vendor/bin/phpunit -c phpunit.xml.dist tests/Processor/CategoryFreebiePromotionTest.php.
 */
class CategoryFreebiePromotionTest extends WebTestCase
{
    protected $jsonData = <<<JSON
    {
        "id": "123",
        "customer-id": "900",
        "items": [
          {
            "product-id": "SW003",
            "quantity": "50",
            "unit-price": "200.00",
            "total": "10000.00"
          }
        ],
        "total": "10000.00"
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
        /** @var CategoryFreebiePromotion $categoryFreebiePromotion */
        $categoryFreebiePromotion = $this->getContainer()->get('app.processor.category_freebie');

        /** @var OrderService $orderService */
        $orderService = $this->getContainer()->get('app.service.order_service');

        $order = $orderService->jsonToOrder($this->jsonData);

        $categoryFreebiePromotion->setOrder($order);
        $categoryFreebiePromotion->process();
        /** @var OrderItem $item */
        $item = $order->getItems()[0];

        $this->assertEquals(50, $item->getRawQuantity());
        $this->assertEquals(60, $item->getQuantity());
    }
}
