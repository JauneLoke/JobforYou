<?php

namespace App\Controller;

use App\Entity\Emplois;
use App\Entity\EmploisUniqueViews;
use App\Entity\TypeContrat;
use App\Entity\TypeExperience;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmploisController extends AbstractController
{
    /**
     * @Route("/trouver-un-job/{page}", defaults={"page"=1}, name="emplois")
     */
    public function emplois(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $search = "";
        $orderBy = array('dateAdd');
        $filters = array();

        if($request->query->has('job-type') && count($request->query->get('job-type'))) {
            foreach ($request->query->get('job-type') as $typeContrat) {
                $filters['typeContrat'][] = (int)$typeContrat;
            }
        }

        if($request->query->has('experience') && count($request->query->get('experience'))) {
            foreach ($request->query->get('experience') as $typeExperience) {
                $filters['typeExperience'][] = (int)$typeExperience;
            }
        }

        if($request->query->has('s') && $request->query->get('s')) {
            $search = trim(strtolower($request->query->get('s')));
        }

        $emploisCount = $em->getRepository(Emplois::class)->countEmplois($search, $filters);
        $emplois = $em->getRepository(Emplois::class)->findEmplois($search, $orderBy, $filters, 10, (10*($page-1)));

        $pagination = array(
            'page' => $page,
            'nbPages' => (ceil($emploisCount / 10) == 0 ? 1 : ceil($emploisCount / 10)),
            'nomRoute' => 'emplois',
            'paramsRoute' => array(
            ),
            'query' => $request->getQueryString()
        );

        return $this->render('emplois/index.html.twig', [
            'emplois' => $emplois,
            'pagination' => $pagination,
            'typeContrat' => $em->getRepository(TypeContrat::class)->findBy(array('active' => 1)),
            'typeExperience' => $em->getRepository(TypeExperience::class)->findBy(array('active' => 1)),
            'controller_name' => 'EmploisController',
            'search' => $search,
            'filters' => $filters
        ]);
    }

    /**
     * @Route("/emploi/{idEmploi}", defaults={"idEmploi"=0}, name="emploi")
     */
    public function emploi($idEmploi = 0, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if((int)$idEmploi == 0) {
            $this->addFlash('warning', 'Désolé, cette offre d\'emploi n\'est plus en ligne.');
            return $this->redirectToRoute('emplois');
        }

        $emploi = $em->getRepository(Emplois::class)->findOneBy(array('id' => (int)$idEmploi));
        if($emploi && ($emploi->getActive() == 1 || ($emploi->getActive() == 2 && $this->getUser() && $this->get('security.authorization_checker')->isGranted('ROLE_ENTREPRISE') && $emploi->getEntreprise() == $this->getUser()->getEntreprise()))) {
            if($emploi->getActive() == 2) {
                $this->addFlash('warning', 'Cette offre est visible que par <b>votre entreprise</b> <i>(brouillon)</i>. <br />Pour la publier, rendez-vous dans "Mes offres d\'emploi"');
            } else {
                $viewed = $em->getRepository(EmploisUniqueViews::class)->findOneBy(array('adresseIp' => $request->getClientIp(), 'Emplois' => $emploi));
                if($viewed) {
                    if($viewed->getDateVisited()->format('U') < strtotime('-3 hours')) {
                        $viewed->setDateVisited(new \DateTime());
                        $emploi->setCountViewed(($emploi->getCountViewed()+1));
                    }
                } else {
                    $viewed = new EmploisUniqueViews();
                    $viewed
                        ->setEmplois($emploi)
                        ->setAdresseIp($request->getClientIp())
                        ->setDateVisited(new \DateTime());

                    $emploi->setCountViewed(($emploi->getCountViewed()+1));

                    $em->persist($viewed);
                }

                $em->flush();
            }

            return $this->render('emplois/emploi.html.twig', [
                'emploi' => $emploi,
                'controller_name' => 'EmploisController',
            ]);
        } else {
            $this->addFlash('warning', 'Désolé, cette offre d\'emploi n\'est plus en ligne.');
            return $this->redirectToRoute('emplois');
        }
    }
}
