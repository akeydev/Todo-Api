<?php

namespace App\DataFixtures;

use App\Story\DefaultTodosStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        DefaultTodosStory::load();
        $manager->flush();
    }
}
