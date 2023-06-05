<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; ++$i) {
            $product = new Product();
            $product->setName($faker->words(3, true));
            $product->setSku($faker->ean8);
            $product->setStock($faker->randomNumber(2, true));
            $product->setPrice($faker->randomNumber(4, true));
            $product->setDescription($faker->words(10, true));
            $product->setImage("https://picsum.photos/seed/$faker->ean8/200");
            $manager->persist($product);
        }

        $manager->flush();
    }
}
