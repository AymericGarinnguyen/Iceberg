<?php


namespace App\Controller\Iceberg;


use App\Entity\Projet;
use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends AbstractController
{
    /**
     * Page d'Accueil
     * @Route("/",
     *     name="default_index")
     */
    public function index(ProjetRepository $projetRepository)
    {
        # Récupération des projets dans BDD
        $projets = $projetRepository->findAllOrderByDateFinInscription();



        return $this->render("default/index.html.twig", [
            'projets' => $projets
        ]);
    }
}