<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\OrderPromotion;
use AppBundle\Entity\Product;
use AppBundle\Entity\Repository\CategoryRepository;
use AppBundle\Service\ManagersCommandService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * vendor/bin/phpunit -c phpunit.xml.dist tests/Service/ManagersCommandServiceTest.php.
 */
class ManagersCommandServiceTest extends WebTestCase
{
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

    public function testListCategory()
    {
        /** @var ManagersCommandService $srvManagersCommand */
        $srvManagersCommand = $this->getContainer()->get('app.service.managers_command');

        $this->assertEquals(
            '<comment>Id        |Enabled   |Category</comment>|<info>3         |1         |Puny Hero</info>|<info>2   '
            .'      |1         |Switches</info>|<info>1         |1         |Tools</info>',
            implode('|', $srvManagersCommand->listCategory())
        );
    }

    public function testListProduct()
    {
        /** @var ManagersCommandService $srvManagersCommand */
        $srvManagersCommand = $this->getContainer()->get('app.service.managers_command');

        $this->assertEquals(
            '<comment>Id             |Enabled   |Code           |Name</comment>|<info>5              |1         '
            .'|PH005          |Deadpool</info>|<comment>Category</comment>|- 3: Puny Hero||<comment>Id             '
            .'|Enabled   |Code           |Name</comment>|<info>2              |1         |TO002          |Giant '
            .'Screw</info>|<comment>Category</comment>|- 1: Tools||<comment>Id             |Enabled   |Code           '
            .'|Name</comment>|<info>6              |1         |PH006          |Hulk</info>|<comment>Category</comment>'
            .'|- 3: Puny Hero||<comment>Id             |Enabled   |Code           |Name</comment>|<info>7             '
            .' |1         |PH007          |Iron Man</info>|<comment>Category</comment>|- 3: Puny Hero||<comment>Id    '
            .'         |Enabled   |Code           |Name</comment>|<info>1              |1         |TO001         '
            .' |Magical Hammer</info>|<comment>Category</comment>|- 1: Tools||<comment>Id             |Enabled   |Code '
            .'          |Name</comment>|<info>8              |1         |PH008          |Spider-Man</info>|<comment>'
            .'Category</comment>|- 3: Puny Hero||<comment>Id             |Enabled   |Code           |Name</comment>|'
            .'<info>4              |1         |SW004          |The "Else"</info>|<comment>Category</comment>|- 2: '
            .'Switches||<comment>Id             |Enabled   |Code           |Name</comment>|<info>3              |1     '
            .'    |SW003          |The "If"</info>|<comment>Category</comment>|- 2: Switches||<comment>Id           '
            .'  |Enabled   |Code           |Name</comment>|<info>9              |1         |PH009         '
            .' |Thor</info>|<comment>Category</comment>|- 3: Puny Hero|',
            implode('|', $srvManagersCommand->listProduct())
        );
    }

    public function testOrderPromotions()
    {
        /** @var ManagersCommandService $srvManagersCommand */
        $srvManagersCommand = $this->getContainer()->get('app.service.managers_command');

        var_dump(implode('|', $srvManagersCommand->listOrderPromotions()));

        $this->assertEquals(
            '<comment>Id        |Enabled   |Code      |Min. Amount |Discount  |Freebie        |Description</comment>|'
            .'<info>1         |1         |AAA001    |1000        |10        |0              |Buy 1000€ get 10%</info>',
            implode('|', $srvManagersCommand->listOrderPromotions())
        );
    }
}
