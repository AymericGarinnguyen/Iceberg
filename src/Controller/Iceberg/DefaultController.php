<?php


namespace App\Controller\Iceberg;


use App\Entity\Projet;
use App\Entity\User;
use App\Form\MembreType;
use App\Form\OrganisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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

        $membreType = $this->createForm(MembreType::class);
        $orgaType = $this->createForm(OrganisateurType::class);

        return $this->render("default/index.html.twig", [
            'projets' => $projets,
            'formMembre' => $membreType->createView(),
            ''
        ]);
    }

    /**
     * VUE MEMBRE
     * Page Membre Profil
     * @Route("/profil_id", name="default_membre_profil")
     */
   public function membreProfil()
  {
return $this->render('Default/VueMembre/membreProfil.html.twig');
  }

} //fin class Default Controller