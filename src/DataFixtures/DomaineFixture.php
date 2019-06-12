<?php

namespace App\DataFixtures;

use App\Entity\Domaine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DomaineFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {

        # Photographie
        $domaine = new Domaine();
        $domaine->setCategorie("Photographie");
        $manager->persist($domaine);

        # Sculpture
        $domaine = new Domaine();
        $domaine->setCategorie("Sculpture");
        $manager->persist($domaine);

        # Peinture
        $domaine = new Domaine();
        $domaine->setCategorie("Peinture");
        $manager->persist($domaine);

        # Dessin
        $domaine = new Domaine();
        $domaine->setCategorie("Dessin");
        $manager->persist($domaine);

        # Théâtre
        $domaine = new Domaine();
        $domaine->setCategorie("Théâtre");
        $manager->persist($domaine);

        # Danse
        $domaine = new Domaine();
        $domaine->setCategorie("Danse");
        $manager->persist($domaine);

        $manager->flush();
    }
}
