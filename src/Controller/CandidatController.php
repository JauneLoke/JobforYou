<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Candidat;
use App\Entity\User;
use App\Entity\Emplois;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class CandidatController extends AbstractController
{
    /**
     * @Route("/candidat", name="candidat")
     */
    public function candidat()
    {
        $em = $this->getDoctrine()->getManager();
        $candidats = $em->getRepository(Candidat::class)->findBy(array('reservationOk' => 1), array('dateAdd' => 'DESC'));


        return $this->render('candidat/candidats.html.twig', [
            'controller_name' => 'CandidatController',
            'candidat' => $candidats
        ]);
    }
    
    /**
     * @Route("/candidats", name="candidats")
     */
    public function listeCandidat() {

        $em = $this->getDoctrine()->getManager();

        $candidats = $em->getRepository(Candidat::class)->findBy(array());

        
        return $this->render('emplois/candidats.html.twig', [
            'controller_name' => 'CandidatController',
            'page_name' => 'Liste des candidats',
            'candidats' => $candidats
        ]);
    }

    /**
     * @Route("/candidat/inscription", name="candidat_inscription")
     */
    public function candidatInscription(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EmailService $emailService)
    {
        if($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
                
            if(!trim($request->request->get('nom')) &&
                !trim($request->request->get('prenom')) &&
                !trim($request->request->get('email')) &&
                !trim($request->request->get('password'))
            ) {
                $this->addFlash('danger', 'Un champ requis n\'a pas été complété.');
            } else {
                if (!filter_var(trim($request->request->get('email')), FILTER_VALIDATE_EMAIL)) {
                    $this->addFlash('danger', 'L\'adresse e-mail saisie semble invalide.');
                } else {
                    $exist = $em->getRepository(User::class)->findOneBy(array('email' => trim($request->request->get('email'))));
                    if(!$exist) {
                        $user = new User();
                        $user
                            ->setEnabled(true)
                            ->setEmail(trim($request->request->get('email')))
                            ->setRoles(array('ROLE_CANDIDAT'))
                            ->setPassword($userPasswordEncoder->encodePassword($user, $request->request->get('password')));

                        $em->persist($user);

                        if($user) {
                            $candidat = new Candidat();
                            $candidat
                                ->setNom($request->request->get('nom'))
                                ->setPrenom($request->request->get('prenom'))
                                ->setUser($user)
                                ->setDateAdd(new \DateTime())
                                ->setDateUpd(new \DateTime());

                            $em->persist($candidat);
                        }

                        $em->flush();

                        $emailService->sendEmail(
                            $user->getEmail(),
                            'Confirmation de votre demande d\'inscription',
                            'email/candidat/registrationConfirmation.html.twig',
                            array(
                                'name' => $request->request->get('prenom').' '.$request->request->get('nom')
                            )
                        );

                        $this->addFlash('success', 'Votre inscription a bien été prise en compte.<br>Un e-mail de confirmation vous a été envoyé.<br>Vous pouvez vous connecter à votre compte dès maintenant !');
                    } else {
                        $this->addFlash('danger', 'Un compte est déjà enregistré avec cette adresse e-mail.');
                    }
                }
            }
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/candidat/candidature", name="candidat_candidature")
     */
    public function candidatCandidature(Request $request, UserPasswordEncoderInterface $userPasswordEncoder) {
        
        if ($request->isMethod('POST') && strlen($request->request->get('description')) > 50) {
            $em = $this->getDoctrine()->getManager();

            $emploi = $em->getRepository(Emplois::class)->find((int)$request->request->get('idEmploi'));

            if($emploi) {
                    $candidatureExist = $em->getRepository(Candidature::class)->findBy(array(
                        'Emplois' => $emploi,
                        'Candidat' => $this->getUser()->getCandidat()
                    ));

                    if($candidatureExist) {
                        $this->addFlash('danger', 'Vous avez déjà postulé à cette offre d\'emploi.');
                    } else {
                        $candidature = new Candidature();
                        $candidature
                            ->setEmplois($emploi)
                            ->setDescription($request->request->get('description'))
                            ->setCandidat($this->getUser()->getCandidat());

                        $em->persist($candidature);

                        $em->flush();

                        $this->addFlash('success', 'Votre candidature a été prise en compte. L\'employeur a été avertit');
                    }                                
            } else {
                $this->addFlash('danger', 'Cette offre d\'emploi n\'existe pas ou n\'est plus disponible.');
            }
        }
        elseif(strlen($request->request->get('description')) < 50 && strlen($request->request->get('description')) > 1) {
            $this->addFlash('danger', 'Le champ message doit être au minimum de 50 caractères.');
        }
        else {
            $this->addFlash('danger', 'Le champ message ne peut pas être vide.');
        }

        return $this->redirectToRoute('emploi', array('idEmploi' => (int)$request->request->get('idEmploi')));
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

    /**
     * @Route("/candidat/{idCandidat}", defaults={"idCandidat"=0}, name="candidat")
     */
    public function candidatProfil($idCandidat = 0, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if((int)$idCandidat == 0) {
            $this->addFlash('warning', 'Désolé, ce profil n\'existe plus.');

            return $this->redirectToRoute('candidats');
        }

        $candidat = $em->getRepository(Candidat::class)->findOneBy(array('id' => (int)$idCandidat));

            $em->flush();

            // dd($candidat);

            return $this->render('emplois/candidat.html.twig', [
                'candidat' => $candidat,
                'controller_name' => 'CandidatController',
            ]);
    }
}