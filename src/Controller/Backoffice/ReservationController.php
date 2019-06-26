<?php

namespace App\Controller\Backoffice;

use App\Entity\Entreprise;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ReservationController extends AbstractController
{
    /**
     * @Route("/dashboard/admin/reservations-soumises/{page}", defaults={"page"=1}, name="dashboard_reservations")
     */
    public function index($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $entreprisesCount = $em->getRepository(Entreprise::class)->countReservation();
        $entreprises = $em->getRepository(Entreprise::class)->findReservation(array('dateAdd' => 'ASC'), 10, (10*($page-1)));

        $pagination = array(
            'page' => $page,
            'nbPages' => (ceil($entreprisesCount / 10) == 0 ? 1 : ceil($entreprisesCount / 10)),
            'nomRoute' => 'dashboard_reservations',
            'paramsRoute' => array(
            )
        );

        return $this->render('backoffice/reservations.html.twig', [
            'controller_name' => 'ReservationController',
            'entreprises' => $entreprises,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/dashboard/admin/entreprises-confirmees/{page}", defaults={"page"=1}, name="dashboard_reservations_confirmees")
     */
    public function reservationConfirmee($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $entreprisesCount = $em->getRepository(Entreprise::class)->count(array('reservationOk' => 1));
        $entreprises = $em->getRepository(Entreprise::class)->findBy(array('reservationOk' => '1'), array('dateAdd' => 'DESC'), 10, (10*($page-1)));

        $pagination = array(
            'page' => $page,
            'nbPages' => (ceil($entreprisesCount / 10) == 0 ? 1 : ceil($entreprisesCount / 10)),
            'nomRoute' => 'dashboard_reservations_confirmees',
            'paramsRoute' => array(
            )
        );

        return $this->render('backoffice/reservationsConfirmees.html.twig', [
            'controller_name' => 'ReservationController',
            'entreprises' => $entreprises,
            'entreprisesCount' => $entreprisesCount,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/dashboard/admin/reservationAccept/{idEntreprise}/{result}", name="dashboard_reservation_accept")
     */
    public function reservationAccept(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EmailService $emailService, $idEntreprise, $result)
    {
        $em = $this->getDoctrine()->getManager();

        if((int)$idEntreprise) {
            $entreprise = $em->getRepository(Entreprise::class)->find((int)$idEntreprise);

            if($entreprise != null) {
                $entreprise
                    ->setReservationOk((bool)$result)
                    ->setDateUpd(new \DateTime());

                $sendMail = false;
                if((bool)$result) {
                    $entreprise->getUser()->setEnabled(true);

                    $password = $this->generatePassword(8);
                    $entreprise->getUser()->setPassword($userPasswordEncoder->encodePassword($entreprise->getUser(), $password));
                    $sendMail = true;
                } else {
                    $entreprise->getUser()->setEnabled(false);
                }

                $em->flush();

                if($sendMail && $password) {
                    $emailService->sendEmail(
                        $entreprise->getUser()->getEmail(),
                        '[Identifiants Job&You] - Votre réservation de stand a été approuvée',
                        'email/entreprise/reservationApprouve.html.twig',
                        array(
                            'password' => $password,
                            'email' => $entreprise->getUser()->getEmail(),
                            'name' => $entreprise->getDenomination()
                        )
                    );
                }

                $this->addFlash('success', 'Réservation de l\'entreprise '.$entreprise->getDenomination().' '.((bool)$result ? 'acceptée' : 'refusée').' avec succès.');
            }
        }

        return $this->redirectToRoute('dashboard_reservations');
    }

    public function generatePassword($length) {
        $password = "";

        $chaine = "abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ023456789+@!$%?&";
        $longeur_chaine = strlen($chaine);

        for($i = 1; $i <= (int)$length; $i++)
        {
            $place_aleatoire = mt_rand(0,($longeur_chaine-1));
            $password .= $chaine[$place_aleatoire];
        }

        return $password;
    }
}
