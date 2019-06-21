<?php

namespace App\Form;

use App\Entity\Domaine;
use App\Entity\Projet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->get('image')->addViewTransformer(new CallbackTransformer(
                function($value) {
                    if(is_string($value)) {
                        return new File('images/'.$value);
                    }
                },
                function($value) {
                    return $value;
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
