<?php

namespace App\DataFixtures;

use App\Entity\Car;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 3; $i++) {
            $product = new Car();
            $product->setMarque('BMW '.$i);
            $product->setModele('Modele '.$i);
            $product->setImmatriculation(mt_rand(1000, 9999).'TBU');
            $manager->persist($product);
        }

        $manager->flush();
    }
}
