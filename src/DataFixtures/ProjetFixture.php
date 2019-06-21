<?php

namespace App\DataFixtures;

use App\Entity\Projet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProjetFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $projet = new Projet();
        $projet->setTitre('Lorem Ipsum')
            ->setDomaine(["Photographie"])
            ->setDateDebutInscription(date_create_from_format('j-m-Y', '17-06-2019'))
            ->setDateFinInscription(date_create_from_format('j-m-Y', '17-07-2019'))
            ->setDescription("Lorem ipsum alea jacta est")
            ->setWebsite("http://www.photo.fest.fr")
            ->setDateDebutEvenement(date_create_from_format('j-m-Y', '01-10-2019'))
            ->setDateFinEvenement(date_create_from_format('j-m-Y', '17-10-2019'))
            ->setImage("01.jpg")
            ->setVille("Paris")
            ->setPays("France")
            ->setUser($this->getReference(UserFixture::PROJET_USER_REFERENCE));
        $manager->persist($projet);
        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            UserFixture::class,
            DomaineFixture::class
        ];
    }
}
