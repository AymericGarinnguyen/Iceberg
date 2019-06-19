<?php


namespace App\Controller\Iceberg;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search",name="search_project")
     */
    public function search()
    {
        $finder = $this->container->get('fos_elastica.finder.app.projet');

        $results = $finder->find('photographie');

        return $this->render('default/filtre.html.twig', [
            'results' => $results
        ]);
    }


}