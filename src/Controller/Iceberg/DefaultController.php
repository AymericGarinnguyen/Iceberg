<?php


namespace App\Controller\Iceberg;


use App\Entity\Domaine;
use App\Entity\Projet;
use App\Repository\ProjetRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
            return $projet->getDateFinInscription()->format('U') > time() && $projet->getDateDebutInscription()->format('U') < time();
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
            return $projet->getDateFinInscription()->format('U') < time();
        });
        # Tri des projets
        usort($projetsFin, function(Projet $projet1, Projet $projet2 ) {
            return $projet2->getDateFinInscription()->format('U') <=>  $projet1->getDateFinInscription()->format('U');
        });



        return $this->render("default/index.html.twig", [
            'projets' => $projets,
            'projetsEnCours' => $projetsEnCours,
            'projetsDebut' => $projetsDebut,
            'projetsFin' => $projetsFin
        ]);
    }

    /**
     * Sidebar
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sidebar(Request $request)
    {
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
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer'
            ])
            ->getForm();

        # Traitement des données POST
        $form->handleRequest($request);

        # Rendu de la vue
        return $this->render('Components/_sidebar.html.twig', [
            'form' => $form->createView()
        ]);
    }
}