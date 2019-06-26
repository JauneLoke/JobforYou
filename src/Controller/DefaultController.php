<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/le-forum", name="forum")
     */
    public function forum()
    {
        return $this->render('default/forum.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/page/{viewName}", defaults={"viewName"=""}, name="page")
     */
    public function page($viewName)
    {
        if(in_array($viewName, array(
            'conditions-generales-de-ventes',
            'mentions-legales'
        ))) {
            return $this->render('default/page/'.$viewName.'.html.twig', [
                'controller_name' => 'DefaultController',
            ]);
        } else {
            $this->addFlash('danger', 'Cette page n\'existe pas ou n\'est plus disponible. <br />Vous avez été rediriger vers notre page d\'accueil.');

            return $this->redirectToRoute('homepage');
        }
    }
}
