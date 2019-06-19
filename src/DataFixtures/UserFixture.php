<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    const PROJET_USER_REFERENCE = 'projet-user';

    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setPrenom("Cedric")
            ->setNom("Obstoy")
            ->setPassword("password")
            ->setEmail(["ced.ob@yahoo.fr"])
            ->setRole(["ROLE_ADMIN"]);
        $manager->persist($user);
        $manager->flush();

        # Partage du user
        $this->addReference(self::PROJET_USER_REFERENCE, $user);
    }
}
