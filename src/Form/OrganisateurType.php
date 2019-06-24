<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom (organisation)',
                'attr' => [
                    'placeholder' => 'Saisissez le nom de votre organisation'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Saisissez votre email'
                ]
            ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $orga = $event->getData();
            $form = $event->getForm();

            // checks if the Product object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$orga || null === $orga->getId()) {
                $form
                    ->add('password', PasswordType::class, [
                        'label' => 'Mot de passe',
                        'attr' => [
                            'placeholder' => 'Saisissez votre mot de passe'
                        ]
                    ])
                    ->add('submit', SubmitType::class, [
                        'label' => "Je m'inscris !",
                        'attr'=> ['class' => 'inscription']
                    ]);

            } else {
                $form->add('submit', SubmitType::class,[
                    'label' => "J'enregistre mes modifications !",
                    'attr'=> ['class' => 'inscription']
                ]);
            }
        });
    } // fin function buildForm

    public function getBlockPrefix()
    {
        return 'organisateur';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['registration'],
        ]);
    }
}
