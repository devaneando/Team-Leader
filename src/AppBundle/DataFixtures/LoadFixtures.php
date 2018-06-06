<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryFreebiePromotion;
use AppBundle\Entity\CheapestDiscountPromotion;
use AppBundle\Entity\OrderPromotion;
use AppBundle\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tools = new Category();
        $tools
            ->setEnabled(true)
            ->setName('Tools');

        $switches = new Category();
        $switches
            ->setEnabled(true)
            ->setName('Switches');

        $punyHero = new Category();
        $punyHero
            ->setEnabled(true)
            ->setName('Puny Hero');

        $products = [
            ['name' => 'magicalHammer', 'value' => 'Magical Hammer'],
            ['name' => 'giantScrew', 'value' => 'Giant Screw'],
            ['name' => 'theIf', 'value' => 'The "If"'],
            ['name' => 'theElse', 'value' => 'The "Else"'],
            ['name' => 'deadpool', 'value' => 'Deadpool'],
            ['name' => 'hulk', 'value' => 'Hulk'],
            ['name' => 'ironMan', 'value' => 'Iron Man'],
            ['name' => 'spiderMan', 'value' => 'Spider-Man'],
            ['name' => 'thor', 'value' => 'Thor'],
        ];

        for ($i=1; $i <= count($products); ++$i) {
            $category = $punyHero;
            $prefix = 'PH';
            if ($i < 5) {
                $category = $switches;
                $prefix = 'SW';
            }
            if ($i < 3) {
                $category = $tools;
                $prefix = 'TO';
            }

            $name = $products[$i-1]['name'];
            $value = $products[$i-1]['value'];
            $$name = new Product();
            $$name
                ->setCode(sprintf('%s%03s', $prefix, $i))
                ->setEnabled(true)
                ->setName($value);
            $$name->addCategory($category);
            $manager->persist($$name);
        }

        // A customer who has already bought for over € 1000, gets a discount of 10% on the whole order.
        // For every product of category "Switches" (id 2), when you buy five, you get a sixth for free.
        // If you buy two or more products of category "Tools" (id 1), you get a 20% discount on the cheapest product

        $orderPromotion = new OrderPromotion();
        $orderPromotion
            ->setCode('AAA001')
            ->setDescription('Buy 1000€ get 10%')
            ->setDiscount(10)
            ->setEnabled(true)
            ->setMinimumAmount(1000);
        $manager->persist($orderPromotion);

        $orderPromotion = new OrderPromotion();
        $orderPromotion
            ->setCode('BBB001')
            ->setDescription('Buy 100€ get a free Spider-Man')
            ->setFreebieItem($spiderMan)
            ->setFreebieQuantity(1)
            ->setEnabled(true)
            ->setMinimumAmount(100);
        $manager->persist($orderPromotion);

        $categoryFreebiePromotion = new CategoryFreebiePromotion();
        $categoryFreebiePromotion
            ->setCategory($switches)
            ->setCode('ZZZ001')
            ->setDescription('Buy 5 switches get a sixty for free.')
            ->setEnabled(true)
            ->setMinimumQuantity(5);
        $manager->persist($categoryFreebiePromotion);

        $cheapestDiscountPromotion = new CheapestDiscountPromotion();
        $cheapestDiscountPromotion
            ->setCategory($tools)
            ->setCode('UUU001')
            ->setDescription('Buy 2 tools get 20% in the cheapes product.')
            ->setDiscount(20)
            ->setEnabled(true)
            ->setMinimumQuantity(2);
        $manager->persist($cheapestDiscountPromotion);

        $manager->flush();
    }
}
