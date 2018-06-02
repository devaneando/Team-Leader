<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Category;
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

//         A customer who has already bought for over € 1000, gets a discount of 10% on the whole order.
        // For every product of category "Switches" (id 2), when you buy five, you get a sixth for free.
        // If you buy two or more products of category "Tools" (id 1), you get a 20% discount on the cheapest product.

        $products = [
            'Magical Hammer',
            'Giant Screw',
            'The "If"',
            'The "Else"',
            'Deadpool',
            'Hulk',
            'Iron Man',
            'Spider-Man',
            'Thor',
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

            $product = new Product();
            $product
                ->setCode(sprintf('%s%03s', $prefix, $i))
                ->setEnabled(true)
                ->setName($products[$i-1]);
            $product->addCategory($category);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
