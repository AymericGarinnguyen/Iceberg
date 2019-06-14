<?php


namespace App\Controller\Iceberg;


use App\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * Page d'Accueil
     * @Route("/",
     *     name="default_index")
     */
    public function index()
    {
        # Récupération des projets dans BDD
        $projets = $this->getDoctrine()
            ->getRepository(Projet::class)
            ->findAll();

        return $this->render("default/index.html.twig", [
            'projets' => $projets
        ]);
    }
}