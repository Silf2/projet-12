<?php

namespace App\DataFixtures;

use App\Entity\Advice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <=10; $i++){
            $user = new User();
            $user->setUsername('User' . $i);
            $user->setPassword($i);
            $user->setPostalCode(12345);
            $user->setAdmin(false);

            $manager->persist($user);
        }

        for ($i = 1; $i <= 12; $i++){
            $advice = new Advice();
            $advice->setContent('Contenus super du conseil  n°' . $i);
            $advice->setMonths([$i]);

            $manager->persist($advice);
        }

        $manager->flush();
    }
}