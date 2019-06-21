<?php


namespace App\Controller\Iceberg;


use App\Controller\ProjetTrait;
use App\Entity\Projet;
use App\Repository\ProjetRepository;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchAll;
use Elastica\Query\Terms;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    use ProjetTrait;
    /**
     * @Route("/filtres",name="filtres_projet")
     */
    public function filters(RepositoryManagerInterface $finder, Request $request, ProjetRepository $projetRepository)
    {
        $post = $request->request;
        # Si form.domaine n'existe pas : redirection vers l'accueil
        if (!isset($post->get('form')['domaine']) && !isset($post->get('form')['date_debut_evenement']) && !isset($post->get('form')['date_debut_inscription']) && !isset($post->get('form')['date_fin_inscription'])) {
            # Redirection
            return $this->redirectToRoute('default_index');
        } else {
            if (isset($post->get('form')['domaine'])) {
                # Recherche par catégorie
                $form = $request->get('form');
                $boolQuery = new BoolQuery();
                $termsQuery = new Terms();

                $termsQuery->setTerms('domaine.id', $form['domaine']);
                $boolQuery->addShould($termsQuery);
                $projets = $finder->getRepository(Projet::class)->find($termsQuery);

            } else {
                $projets = $projetRepository->findAll();
            }


            if (isset($post->get('form')['date_debut_evenement']) XOR (!isset($post->get('form')['date_debut_evenement']) && !isset($post->get('form')['date_debut_inscription']) && !isset($post->get('form')['date_fin_inscription']))) {
                # Récupération des projets en cours
                $projetsEnCours = array_filter($projets, function (Projet $projets) {
                    return $projets->getDateFinInscription()->format('U') > time() && $projets->getDateDebutInscription()->format('U') < time();
                });
                # Tri des projets en cours
                usort($projetsEnCours, function (Projet $projet1, Projet $projet2) {
                    return $projet1->getDateFinInscription()->format('U') <=> $projet2->getDateFinInscription()->format('U');
                });
            } else {
                $projetsEnCours = [];
            }

            if (isset($post->get('form')['date_debut_inscription']) XOR (!isset($post->get('form')['date_debut_evenement']) && !isset($post->get('form')['date_debut_inscription']) && !isset($post->get('form')['date_fin_inscription']))) {
                # Récupération des projets à venir
                $projetsDebut = array_filter($projets, function (Projet $results) {
                    return $results->getDateDebutInscription()->format('U') > time();
                });
                # Tri des projets à venir
                usort($projetsDebut, function (Projet $projet1, Projet $projet2) {
                    return $projet1->getDateDebutInscription()->format('U') <=> $projet2->getDateDebutInscription()->format('U');
                });
            } else {
                $projetsDebut = [];
            }

            if (isset($post->get('form')['date_fin_inscription']) XOR (!isset($post->get('form')['date_debut_evenement']) && !isset($post->get('form')['date_debut_inscription']) && !isset($post->get('form')['date_fin_inscription']))) {
                # Récupération des projets fermés
                $projetsFin = array_filter($projets, function (Projet $results) {
                    return $results->getDateFinInscription()->format('U') < time();
                });
                # Tri des projets fermés
                usort($projetsFin, function (Projet $projet1, Projet $projet2) {
                    return $projet2->getDateFinInscription()->format('U') <=> $projet1->getDateFinInscription()->format('U');
                });
            } else {
                $projetsFin = [];
            }
        }
        return $this->render('default/filtre.html.twig', [
            'projetsEnCours' => $projetsEnCours,
            'projetsDebut' => $projetsDebut,
            'projetsFin' => $projetsFin
        ]);

    }

    /**
     * @Route("/recherche", name="recherche_projet")
     */
    public function search(RepositoryManagerInterface $finder, Request $request)
    {
        $form = $request->get('form');
        $boolQuerry = new BoolQuery();
        $matchAllQuery = new MatchAll();
        $results = $finder->getRepository(Projet::class)->find($this->slugify($form['description']));

        # Récupération des projets en cours
        $projetsEnCours = array_filter($results, function (Projet $results) {
            return $results->getDateFinInscription()->format('U') > time() && $results->getDateDebutInscription()->format('U') < time();
        });
        # Tri des projets
        usort($projetsEnCours, function (Projet $projet1, Projet $projet2) {
            return $projet1->getDateFinInscription()->format('U') <=> $projet2->getDateFinInscription()->format('U');
        });

        # Récupération des projets à venir
        $projetsDebut = array_filter($results, function (Projet $results) {
            return $results->getDateDebutInscription()->format('U') > time();
        });
        # Tri des projets
        usort($projetsDebut, function (Projet $projet1, Projet $projet2) {
            return $projet1->getDateDebutInscription()->format('U') <=> $projet2->getDateDebutInscription()->format('U');
        });


        # Récupération des projets fermés
        $projetsFin = array_filter($results, function (Projet $results) {
            return $results->getDateFinInscription()->format('U') < time();
        });
        # Tri des projets
        usort($projetsFin, function (Projet $projet1, Projet $projet2) {
            return $projet2->getDateFinInscription()->format('U') <=> $projet1->getDateFinInscription()->format('U');
        });

        return $this->render('default/filtre.html.twig', [
            'projetsEnCours' => $projetsEnCours,
            'projetsDebut' => $projetsDebut,
            'projetsFin' => $projetsFin
        ]);
    }
}