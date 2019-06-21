<?php


namespace App\Controller\Iceberg;

use App\Controller\ProjetTrait;
use App\Entity\Domaine;
use App\Entity\Projet;
use App\Entity\User;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProjetController
 * @package App\Controller\Iceberg
 */
class ProjetController extends AbstractController
{
    use ProjetTrait;


    /**
     * Page Formulaire pour rédiger un appel à projet
     * @IsGranted("ROLE_ORGANISATEUR")
     * @Route("/creer-un-projet",
     *     name="projet_new")
     */
    public function newProjet(Request $request)
    {
        # Récupération d'un organisateur connecté
        $user = $this->getUser();


        #Création d'un nouvel appel
        $projet = new Projet();

        # Attribution d'un auteur à un nouvel appel
        $projet->setUser($user);

        # Création du formulaire
        $form = $this->createFormBuilder($projet)
            ->add('titre', TextType::class, [
                'label' => "Titre de l'appel",
                'attr' => [
                    'placeholder' => "Titre de l'appel"
                    ]
            ])
            ->add('domaine', EntityType::class, [
                'class' => Domaine::class,
                'choice_label' => 'categorie',
                'expanded' => true,
                'multiple' => true
            ])
            ->add('dateDebutInscription', DateType::class, [
                'widget' => 'single_text',
                'empty_data' => null,
                'invalid_message' => "Vous devez choisir une date de début d'inscription"
            ])
            ->add('dateFinInscription', DateType::class, [
                'widget' => 'single_text',
                'empty_data' => null,
                'invalid_message' => "Vous devez choisir une date de fin d'inscription"
            ])
            ->add('description', TextareaType::class)
            ->add('website', UrlType::class)
            ->add('dateDebutEvenement', DateType::class, [
                'widget' => 'single_text',
                'empty_data' => null,
                'invalid_message' => "Vous devez choisir une date de début d'évènement"
            ])
            ->add('dateFinEvenement', DateType::class, [
                'widget' => 'single_text',
                'empty_data' => null,
                'invalid_message' => "Vous devez choisir une date de fin d'évènement"
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'attr' => [
                    'class' => 'dropify'
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville'
                ]
            ])
            ->add('pays', CountryType::class, [
                'choice_loader' => null,
                'choices' => [
                    'France' => 'france',
                    'Belgique' => 'belgique',
                    'Luxembourg' => 'luxembourg',
                    'Suisse' => 'Suisse',
                    'Allemagne' => 'Allemagne',
                    'Pays-Bas' => 'Pays-Bas',
                    'Italie' => 'Italie',
                    'Espagne' => 'Espagne',
                    'Portugal' => 'Portugal',
                    'Angleterre' => 'Angleterre'
                ]
            ])
            ->add('budget', NumberType::class, [
                'label' => 'Montant de la récompense financière',
                'invalid_message' => 'Votre saisie ne doit comporter que des chiffres',
                'attr' => [
                    'placeholder' => 'optionnel'
                ]
            ])
            ->add('frais', NumberType::class, [
                'label' => 'Frais de participation',
                'invalid_message' => 'Votre saisie ne doit comporter que des chiffres',
                'attr' => [
                    'placeholder' => 'optionnel'
                ]
            ])
            ->add('document', FileType::class, [
                'label' => 'documents de présentation (optionnel)',
                'attr' => [
                    'class' => 'dropify'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier'
                ])
            ->getForm();

        # Traitement des données POST (getsion des erreurs)
        $form->handleRequest($request);

        # Si le formulaire est valide
        if($form->isSubmitted() && $form->isValid()) {

            # ########### Gestion des Null ##############
            # Gestion du Null pour "budget"
            $budget = $projet->getBudget();
            if($budget === Null) {
                $projet->setBudget(0);
            }

            # Gestion du Null pour "frais"
            $frais = $projet->getFrais();
            if($frais === Null) {
                $projet->setFrais(0);
            }

            # ########### Upload de l'image ##############
            /**
             * @var UploadedFile $image
             */
            $image = $projet->getImage();

            # Rennomer l'image
            $fileName = $this->slugify($projet->getTitre()).'.'.$image->guessExtension();

            # Déplacer l'image dans le dossier "public/image"
            try {
                $image->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            # Mise à jour du nom de l'image
            $projet->setImage($fileName);

            # ################### Upload du PDF ####################
            /**
             * @var UploadedFile $document
             */
            $document = $projet->getDocument();

            if($document != Null) {
                # Rennomer le document
                $docName = $this->slugify($projet->getTitre()).'.'.$document->guessExtension();

                # Déplacer le document dans le dossier "public/PDF"
                try {
                    $document->move(
                        $this->getParameter('documents_directory'),
                        $docName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                # Mise à jour du nom du document
                $projet->setDocument($docName);
            }


            # Sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($projet);
            $em->flush();

            # Notification flash
            $this->addFlash('notice',
                'Votre projet a bien été sauvegardé');

            #Redirection
            return $this->redirectToRoute("default_index");

        } // Fin $form->isSubmitted

        # Rendu vers formProjet.html.twig
        return $this->render('Projet/formProjet.html.twig', [
            'form' => $form->createView()
        ]);
    } ######################## FIN de function newProjet ###########################


    /**
     * Page vue des appels à projet de l'organisateur
     * @IsGranted("ROLE_ORGANISATEUR")
     * @Route("/projets", name="projet_vue")
     */
    public function vueProjets()
    {
        # Récupération des projets dans BDD
        $projets = $this->getDoctrine()
            ->getRepository(Projet::class)
            ->findBy(['user' => $this->getUser()]);

        # Rendu de la vue
        return $this->render('Projet/vueProjets.html.twig', [
            'projets' => $projets
        ]);

    } ################## Fin de function vueProjets ##########################


    /**
     * Page de Modification des appels à projet
     * @IsGranted("ROLE_ORGANISATEUR")
     * @Route("/modifier-mon-projet", name="projet_ma_modification")
     */
    public function modificationProjet(Projet $projet, EntityManagerInterface $entityManager)
    {

        # Récupération d'un organisateur connecté
        $user = $this->getUser();

        # Suppresion du projet du membre connecté
        $projets= $user->getProjets();

        # Rendu de la vue
        return $this->render('/Projet/vueProjets.html.twig', [
            'projets' => $projets
        ]);

    } ################## Fin de function modificationProjets ##########################


    /**
     * Suppression des appels à projet de l'organisateur connecté
     * @Route("/supprimer-mon-projet/{id}", name="projet_ma_suppression")
     */
    public function supprimerProjetOrga(Projet $projet, EntityManagerInterface $entityManager)
    {
        # Récupération d'un membre connecté
        $user = $this->getUser();

        $entityManager->remove($projet);
        $entityManager->flush();

        # Rendu de la vue
        return $this->redirectToRoute('projet_vue');
        
    } ################## Fin de function supprimerProjetOrga ##########################


    /**
     * Suppresion des appels à projets par l'Admin
     * @IsGranted("ROLE_ADMIN")
     * @Route("/supprimer-projet/{id}", name="projet_supprimer")
     */
    public function supprimerProjetAdmin($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $projets = $entityManager->getRepository(Projet::class)->find($id);
        $entityManager->remove($projets);
        $entityManager->flush();

        # Rendu de la vue
        return $this->redirectToRoute('user_liste_projet');
    } ################## Fin de function supprimerProjetAdmin ##########################


    /**
     * Ajout en favori
     * @Route("/ajouter-favori/{id}", name="projet_ajouter_favori")
     */
    public function newFavori(Projet $projet, EntityManagerInterface $entityManager)
    {
        # Récupération d'un membre connecté
        $user = $this->getUser();
        /** @var User $user */
        $user->addFavori($projet);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('default_index');
    } ################## Fin de function newFavori ##########################

    /**
     * Liste des favoris
     * @Route("/liste_favori", name="projet_liste_favori")
     *
     */
    public function listeFavori()
    {
         # Récupération d'un membre connecté
         $user = $this->getUser();

         # Récupération des favoris du membre connecté
         $favoris= $user->getFavoris();

        # Rendu de la vue
        return $this->render('/Projet/favori.html.twig', [
            'favoris' => $favoris
        ]);
    } ################## Fin de function listeFavori ##########################

    /**
     * Suppresion du favori
     * @Route("/supprimer-favori/{id}", name="projet_supprimer_favori")
     */
    public function supprimerFavori(Projet $projet, EntityManagerInterface $entityManager)
    {
        # Récupération d'un membre connecté
        $user = $this->getUser();
        /** @var User $user */
        $user->removeFavori($projet);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('projet_liste_favori');
        
    }
}