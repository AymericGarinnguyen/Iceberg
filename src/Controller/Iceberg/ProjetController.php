<?php


namespace App\Controller\Iceberg;

use App\Controller\ProjetTrait;
use App\Entity\Domaine;
use App\Entity\Projet;
use App\Entity\User;

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
     * @Route("/auteur/creer-un-appel",
     *     name="projet_new")
     */
    public function newProjet(Request $request)
    {
        # Récupération d'un auteur
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find(5);

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
                'multiple' => true,
                'label' => 'Catégorie'
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
    }
}