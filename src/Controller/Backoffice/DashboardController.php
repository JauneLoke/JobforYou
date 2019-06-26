<?php

namespace App\Controller\Backoffice;

use App\Entity\Emplois;
use App\Entity\Entreprise;
use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();
        $renderVars = array();

        if($authChecker->isGranted('ROLE_ADMIN')) {
            $reservationCount = $em->getRepository(Entreprise::class)->countReservation();
            $reservedCount = $em->getRepository(Entreprise::class)->count(array('reservationOk' => true));
            $emploisCount = $em->getRepository(Emplois::class)->count(array('active' => true));
            $renderVars['reservationCount'] = $reservationCount;
            $renderVars['reservedCount'] = $reservedCount;
            $renderVars['emploisCount'] = $emploisCount;
        }

        if($authChecker->isGranted('ROLE_ENTREPRISE')) {
            $emploisCount = $em->getRepository(Emplois::class)->count(array('active' => 1));
            $renderVars['emploisCount'] = $emploisCount;
        }

        $notifications = $em->getRepository(Notification::class)->findBy(array('seen' => 0, 'User' => $this->getUser()), array('dateAdd' => 'DESC'), 20);

        return $this->render('backoffice/index.html.twig', array_merge([
            'controller_name' => 'DashboardController',
            'notifications' => $notifications
        ], $renderVars));
    }
}
