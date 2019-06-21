<?php


namespace App\Controller\Iceberg;


use App\Entity\Projet;
use App\Entity\User;
use App\Form\MembreType;
use App\Form\OrganisateurType;
use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
        $projets = $projetRepository->findAll();

        # Récupération des projets dans BDD
        $projetsEnCours = array_filter($projets, function(Projet $projet){
            return $projet->getDateFinInscription()->format('U') > time() && $projet->getDateDebutInscription()->format('U') < time();
        });

        # Récupération des projets dans BDD
        $projetsDebut = array_filter($projets, function(Projet $projet){
            return $projet->getDateDebutInscription()->format('U') > time();
        });

        # Récupération des projets dans BDD
        $projetsFin = array_filter($projets, function (Projet $projet){
            return $projet->getDateFinInscription()->format('U') < time();
        });

        usort($projetsEnCours, function(Projet $projet1, Projet $projet2){
            return $projet1->getDateFinInscription()->format('U') <=>  $projet2->getDateFinInscription()->format('U');
        });

        usort($projetsDebut, function(Projet $projet1, Projet $projet2){
            return $projet1->getDateDebutInscription()->format('U') <=>  $projet2->getDateDebutInscription()->format('U');
        });


        usort($projetsFin, function(Projet $projet1, Projet $projet2 ) {
            return $projet2->getDateFinInscription()->format('U') <=>  $projet1->getDateFinInscription()->format('U');
        });


        $membreType = $this->createForm(MembreType::class);
        $orgaType = $this->createForm(OrganisateurType::class);

        return $this->render("default/index.html.twig", [
            'formMembre' => $membreType->createView(),
            'projetsEnCours' => $projetsEnCours,
            'projetsDebut' => $projetsDebut,
            'projetsFin' => $projetsFin
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

    /**
     * Page vue des appels à projet de l'organisateur
     * @Route("/projets_orga", name="projets_orga_liste")
     */
    public function projetsOrgaListe()
    {
        # Récupération des projets dans BDD
        $projets = $this->getDoctrine()
            ->getRepository(Projet::class)
            ->findBy(['user' => $this->getUser()]);

        # Rendu de la vue
        return $this->render('/Projet/listeProjetsOrga.html.twig', [
            'projets' => $projets
        ]);

    } ################## Fin de function vueProjets ##########################


} //fin class Default Controller