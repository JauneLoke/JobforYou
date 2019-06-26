<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\User;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EntrepriseController extends AbstractController
{
    /**
     * @Route("/entreprises", name="entreprises")
     */
    public function entreprises()
    {
        $em = $this->getDoctrine()->getManager();
        $entreprises = $em->getRepository(Entreprise::class)->findBy(array('reservationOk' => 1), array('dateAdd' => 'DESC'));


        return $this->render('entreprise/entreprises.html.twig', [
            'controller_name' => 'EntrepriseController',
            'page_name' => 'Liste des entreprises',
            'entreprises' => $entreprises
        ]);
    }

    /**
     * @Route("/entreprise/inscription", name="entreprise_inscription")
     */
    public function entrepriseInscription(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EmailService $emailService)
    {
        if($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();

            if($request->request->get('email')) {
                if(!trim($request->request->get('denomination')) ||
                    !trim($request->request->get('email')) ||
                    !trim($request->request->get('surface')) ||
                    !trim($request->request->get('adresse')) ||
                    !trim($request->request->get('codePostal')) ||
                    !trim($request->request->get('ville')) ||
                    !trim($request->request->get('telephone')) ||
                    !trim($request->request->get('siret')) ||
                    !trim($request->request->get('nafApe'))
                ){
                    $this->addFlash('danger', 'Un champ requis n\'a pas été complété.');
                } else {
                    if (!filter_var(trim($request->request->get('email')), FILTER_VALIDATE_EMAIL)) {
                        $this->addFlash('danger', 'L\'adresse e-mail saisie semble invalide.');
                    } elseif (!in_array((int)trim($request->request->get('surface')), array(9, 18, 27))) {
                        $this->addFlash('danger', 'La surface souhaitée est invalide. Valeurs attendu : 9/18/27 m².');
                    } else {
                        $exist = $em->getRepository(User::class)->findOneBy(array('email' => trim($request->request->get('email'))));
                        if($exist === null) {
                            $user = new User();
                            $user
                                ->setEnabled(false)
                                ->setEmail(trim($request->request->get('email')))
                                ->setRoles(array('ROLE_ENTREPRISE'))
                                ->setPassword($userPasswordEncoder->encodePassword($user, $this->generatePassword(8)));
                            $em->persist($user);

                            if($user) {
                                $entreprise = new Entreprise();
                                $entreprise
                                    ->setDateAdd(new \DateTime())
                                    ->setDateUpd(new \DateTime())
                                    ->setUser($user)
                                    ->setDenomination($request->request->get('denomination'))
                                    ->setNomMarque($request->request->get('nomMarque'))
                                    ->setSurface((int)$request->request->get('surface'))
                                    ->setAdresse($request->request->get('adresse'))
                                    ->setCodePostal($request->request->get('codePostal'))
                                    ->setVille($request->request->get('ville'))
                                    ->setTelephone($request->request->get('telephone'))
                                    ->setSiteWeb($request->request->get('siteWeb'))
                                    ->setSiret($request->request->get('siret'))
                                    ->setNafApe($request->request->get('nafApe'));

                                $em->persist($entreprise);
                            }

                            $em->flush();

                            $emailService->sendEmail(
                                $user->getEmail(),
                                'Confirmation de votre demande d\'inscription au Forum Job&You',
                                'email/entreprise/demandeInscription.html.twig',
                                array(
                                    'name' => $request->request->get('denomination')
                                )
                            );

                            $emailService->sendEmail(
                                'frederic.joos@ideeconseil.com',
                                'Nouvelle demande de réservation de stand',
                                'email/backoffice/newReservation.html.twig',
                                array(
                                    'name' => $request->request->get('denomination')
                                )
                            );

                            $this->addFlash('success', 'Votre demande d\'inscription a bien été prise en compte. Nous reviendrons vers vous dans les plus bref délais afin de finaliser votre inscription au forum.<br>Un e-mail de confirmation vous a été envoyé.');

                            return $this->redirectToRoute('entreprise_inscription');
                        } else {
                            $this->addFlash('danger', 'Un compte existe déjà avec cette adresse e-mail.');
                        }
                    }
                }
            }
        }

        return $this->render('entreprise/inscription.html.twig', [
            'controller_name' => 'EntrepriseController',
            'page_name' => 'Réserver un stand',
            'formValues' => $request->request->all()
        ]);
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
