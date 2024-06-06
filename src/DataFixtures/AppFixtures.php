<?php

namespace App\DataFixtures;

use App\Entity\Advice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setUsername('Admin');
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, "password"));
        $admin->setPostal_code(12345);
        $manager->persist($admin);

        for ($i = 1; $i <=10; $i++){
            $user = new User();
            $user->setUsername('User' . $i);
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
            $user->setPostal_code(12345);

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