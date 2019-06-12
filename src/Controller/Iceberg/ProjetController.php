<?php


namespace App\Controller\Iceberg;

use App\Entity\Domaine;
use App\Entity\Projet;
use App\Entity\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjetController extends AbstractController
{
    /**
     * Page Formulaire pour rédiger un appel à projet
     * @Route("/auteur/creer-un-appel",
     *     name="projet_new")
     */
    public function newProjet()
    {
        # Récupération d'un auteur
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find(3);

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
            ->add('DateDebutInscription', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('DateFinInscription', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('description', TextareaType::class)
            ->add('website', UrlType::class)
            ->add('DateDebutEvenement', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('DateFinEvenement', DateType::class, [
                'widget' => 'single_text'
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
            ->getForm();

        return $this->render('Projet/formProjet.html.twig', [
            'form' => $form->createView()
        ]);

    }
}