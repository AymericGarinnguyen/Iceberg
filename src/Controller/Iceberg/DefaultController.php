<?php


namespace App\Controller\Iceberg;


use App\Entity\Domaine;
use App\Entity\Projet;
use App\Entity\User;
use App\Form\MembreType;
use App\Form\OrganisateurType;
use App\Repository\ProjetRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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


        # Récupération des projets en cours
        $projetsEnCours = array_filter($projets, function(Projet $projet){
            return $projet->getDateFinInscription()->format('U') > (time() - 86400 )&& $projet->getDateDebutInscription()->format('U') < time();
        });
        # Tri des projets
        usort($projetsEnCours, function(Projet $projet1, Projet $projet2){
            return $projet1->getDateFinInscription()->format('U') <=>  $projet2->getDateFinInscription()->format('U');
        });


        # Récupération des projets à venir
        $projetsDebut = array_filter($projets, function(Projet $projet){
            return $projet->getDateDebutInscription()->format('U') > time();
        });
        # Tri des projets
        usort($projetsDebut, function(Projet $projet1, Projet $projet2){
            return $projet1->getDateDebutInscription()->format('U') <=>  $projet2->getDateDebutInscription()->format('U');
        });


        # Récupération des projets fermés
        $projetsFin = array_filter($projets, function (Projet $projet){
            return $projet->getDateFinInscription()->format('U') < (time() - 86400 );
        });
        # Tri des projets
        usort($projetsFin, function(Projet $projet1, Projet $projet2 ) {
            return $projet2->getDateFinInscription()->format('U') <=>  $projet1->getDateFinInscription()->format('U');
        });


        $membreType = $this->createForm(MembreType::class);
        $orgaType = $this->createForm(OrganisateurType::class);


        # Récupération d'un membre connecté
        $user = $this->getUser();
        # Récupération des favoris du membre connecté
        $favoris= $user ? $user->getFavoris('projet') : [];

        return $this->render("default/index.html.twig", [
            'formMembre' => $membreType->createView(),
            'projetsEnCours' => $projetsEnCours,
            'projetsDebut' => $projetsDebut,
            'projetsFin' => $projetsFin,
            'favoris' => $favoris
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
     * @Route("/organisateur/{id<\d+>}", name="projets_orga_liste")
     */
    public function projetsOrgaListe(User $user)
    {
        # Récupération des projets dans BDD
        $projets = $user->getProjets();

        # Rendu de la vue
        return $this->render('/Projet/listeProjetsOrga.html.twig', [
            'projets' => $projets,
            'user' => $user
        ]);

    } ################## Fin de function vueProjets ##########################


 //fin class Default Controller
    /**
     * Sidebar
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sidebar()
    {
        $recherche = new Projet();

        # Création du formulaire
        $search = $this->createFormBuilder($recherche)
            ->add('description', TextType::class, [
                'attr' => [
                    'placeholder' => 'Rechercher',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn btn-secondary btn-sm'
                ]
            ])
            ->getForm();


        $filtres = new Projet();

        # Création du formulaire
        $form = $this->createFormBuilder($filtres)
            ->add('domaine', EntityType::class, [
                'class' => Domaine::class,
                'choice_label' => 'categorie',
                'expanded' => true,
                'multiple' => true,
                'label' => 'Catégorie'
            ])
            ->add('date_debut_evenement', CheckboxType::class, [
                'label' => 'En cours',
                'compound' => true
            ])
            ->add('date_debut_inscription', CheckboxType::class, [
                'label' => 'A venir',
                'compound' => true
            ])
            ->add('date_fin_inscription', CheckboxType::class, [
                'label' => 'Terminées',
                'compound' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => [
                    'class' => 'btn btn-secondary btn-sm'
                ]
            ])
            ->getForm();


        # Rendu de la vue
        return $this->render('Components/_sidebar.html.twig', [
            'search' => $search->createView(),
            'form' => $form->createView()
        ]);
    }
}//fin class Default Controller