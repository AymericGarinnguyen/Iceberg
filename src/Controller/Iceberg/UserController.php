<?php


namespace App\Controller\Iceberg;


use App\Entity\Projet;
use App\Entity\User;
use App\Form\MembreType;
use App\Form\OrganisateurType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * Page d'inscription
     * @Route("/inscription", name="user_inscription")
     */
    public function NewInscription(Request $request,
                                   UserPasswordEncoderInterface $passwordEncoder)
    {
        #FORM 1
        #Création membre
        $membre = new User();
        $membre->setRoles(['ROLE_MEMBRE']);


        #Création du formulaire inscription membre
        $form1 = $this->createForm(MembreType::class, $membre);


        # Traitement des données $_POST
        # Vérification des données grâce aux Asserts
        # Hydratation de notre objet Membre
        $form1->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {

            # Encodage du mot de passe
            $membre->setPassword(
                $passwordEncoder->encodePassword(
                    $membre,
                    $membre->getPassword()
                )
            );

            # Insertion dans la BDD (EntityManager $em)
            $em = $this->getDoctrine()->getManager();
            $em->persist($membre);
            $em->flush();

            # Notification
            $this->addFlash('notice',
                'Félicitation, vous pouvez vous connecter !');

            # Redirection
            return $this->redirectToRoute('user_connexion');
        }


        # FORM 2
        # Création organisateur
        $organisateur = new User();
        $organisateur->setRoles(['ROLE_ORGANISATEUR']);

        #Création du formulaire inscription organisateur
        $form2 = $this->createForm(OrganisateurType::class, $organisateur);

        # Form 2 (organisateur)
        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {

            # Encodage du mot de passe
            $organisateur->setPassword(
                $passwordEncoder->encodePassword(
                    $organisateur,
                    $organisateur->getPassword()
                )
            );

            # Insertion dans la BDD (EntityManager $em)
            $em = $this->getDoctrine()->getManager();
            $em->persist($organisateur);
            $em->flush();

            # Notification
            $this->addFlash('notice',
                'Félicitation, vous pouvez vous connecter !');

            # Redirection
            return $this->redirectToRoute('user_connexion');
        }

        # Rendu de la vue
        return $this->render('User/inscription.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
        ]);
    }

    ################################# Fin fonction NewInscriptionMembre ############################################


    /**
     * @Route("/connexion", name="user_connexion")
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connexion(AuthenticationUtils $authenticationUtils)
    {

        # Concevoir le formulaire de connexion
        # 3 champs : email, password, submit
        $form = $this->createFormBuilder([
            'email' => $authenticationUtils->getLastUsername()
        ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Saisissez votre email'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Saisissez votre mot de passe'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Se connecter'
            ])
            ->getForm();


        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('home');
        }

        # Rendu de la vue
        return $this->render('user/connexion.html.twig', [
            'form' => $form->createView(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
        dump($form);
        die();
    }
    ################################# Fin fonction connexion ############################################

    /**
     * Déconnexion d'un user
     * @Route("/deconnexion", name="user_deconnexion")
     */
    public function deconnexion()
    {

    }
    ################################# Fin fonction déconnexion ############################################

    /**
     * Modification du profil membre
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/membre_profil", name="user_membre_profil")
     */
    public function profilMembreModif(Request $request)
    {
        # Formulaire d'inscription

        # Création d'un Membre
        $membre = new User();
        $membre->setRoles(['ROLE_MEMBRE']);

        # 1. Création du Formulaire (FormBuilder)
        $form = $this->createFormBuilder($membre)
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Saisissez votre prénom'
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Saisissez votre nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Saisissez votre email'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Je m'inscris !"
            ])
            ->getForm()
        ;

        # Traitement des données $_POST
        # Vérification des données grâce aux Asserts
        # Hydratation de notre objet Membre
        $form->handleRequest( $request );

        if($form->isSubmitted() && $form->isValid()) {



            # 3. Insertion dans la BDD (EntityManager $em)
            $em = $this->getDoctrine()->getManager();
            $em->persist($this);
            $em->flush();

            # Notification
            $this->addFlash('notice',
                'Félicitation, vous pouvez vous connecter !');

        }

        # Rendu de la vue
        return $this->render('/user/membre/modification.html.twig', [
            'form' => $form->createView()
        ]);
    } #################### Fin de function ProfilMembreModif ##########################


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("organisateur_profil", name="user_organisateur_profil")
     */
    public function profilOrganisateurModif(Request $request)
    {
        # Formulaire d'inscription

        # Création d'un Membre
        $organisateur = new User();
        $organisateur->setRoles(['ROLE_ORGANISATEUR']);

        # 1. Création du Formulaire (FormBuilder)
        $form = $this->createFormBuilder($organisateur)
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Saisissez votre nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Saisissez votre email'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Je m'inscris !"
            ])
            ->getForm()
        ;

        # Traitement des données $_POST
        # Vérification des données grâce aux Asserts
        # Hydratation de notre objet Membre
        $form->handleRequest( $request );

        if($form->isSubmitted() && $form->isValid()) {

            # 3. Insertion dans la BDD (EntityManager $em)
            $em = $this->getDoctrine()->getManager();
            $em->persist($this);
            $em->flush();

            # Notification
            $this->addFlash('notice',
                'Félicitation, vous pouvez vous connecter !');

        }

        # Rendu de la vue
        return $this->render('/user/organisateur/modificationOrga.html.twig', [
            'form' => $form->createView()
        ]);
    } #################### Fin de function ProfilMembreModif ##########################



    #################################################################
    #                                                               #
    #                           ADMIN                               #
    #                                                               #
    #################################################################

    /**
     * Page liste des appels à projet
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/liste_projet", name="user_liste_projet")
     */
    public function listeProjet()
    {
        # Récupération des projets dans BDD
        $projets = $this->getDoctrine()
            ->getRepository(Projet::class)
            ->findAll();

        # Rendu de la vue
        return $this->render('/user/admin/listeProjet.html.twig', [
            'projets' => $projets
        ]);
    } ####################### Fin de function listeProjet ###########################


    /**
     * Page liste des users
     * @Route("/liste_user", name="user_liste_user")
     */
    public function listeUser()
    {
        # Récupération des projets dans BDD
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        # Rendu de la vue
        return $this->render('/user/admin/listeUser.html.twig', [
            'users' => $users
        ]);
    } ####################### Fin de function listeUser ###########################

    /**
     * Supprimer un user
     * @IsGranted("ROLE_ADMIN")
     * @Route("/supprimer-user/{id}", name="user_supprimer")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerUser($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($users);
        $entityManager->flush();

        # Rendu de la vue
        return $this->redirectToRoute('user_liste_user');
    } ################## Fin de function supprimerUser ##########################


} ############################### FIN de la Class User ##################################
