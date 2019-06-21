<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setPrenom("Cedric")
            ->setNom("Obstoy")
            ->setPassword("password")
            ->setEmail(["ced.ob@yahoo.fr"])
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);
        $manager->flush();

        # Partage du user
    }
}
