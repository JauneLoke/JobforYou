<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login_check", name="login_check")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if($request->isMethod('POST')) {
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            if($this->isGranted('ROLE_USER')) {
                if($this->isGranted('ROLE_CANDIDAT')) {
                    $this->addFlash('success', 'Nous sommes heureux de vous revoir '.$this->getUser()->getCandidat()->getPrenom());
                }
                return $this->redirectToRoute('homepage');
            }
        } else {
            return $this->redirect($this->generateUrl('homepage')."#login");
        }
    }

    /**
     * @Route("/mot-de-passe-oublie", name="forgot_password")
     */
    public function forgotPassword(
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder,
        EmailService $emailService,
        TokenGeneratorInterface $tokenGenerator): Response
    {
        if($request->isMethod('POST') && $request->request->get('email')) {

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(array('email' => $request->request->get('email')));

            if($user === null) {
                $this->addFlash('danger', 'Désolé, nous ne trouvons aucun compte lié à cette adresse e-mail.');
                return $this->redirectToRoute('forgot_password');
            }

            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('forgot_password');
            }

            $url = $this->generateUrl('forgot_password_reset', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $emailService->sendEmail(
                $user->getEmail(),
                'Réinitialisation de votre mot de passe',
                'email/security/forgotPassword.html.twig',
                array(
                    'resetLink' => $url
                )
            );

            $this->addFlash('success', 'Un lien sécurisé vous permettant de réinitialiser votre mot de passe vous a été envoyé par e-mail.');

            return $this->redirectToRoute('forgot_password');
        }

        return $this->render('security/forgot-password.html.twig');
    }

    /**
     * @Route("/forgot-password/{token}", name="forgot_password_reset")
     */
    public function forgotPasswordReset(Request $request, string $token, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        if($request->isMethod('POST')) {

            if($request->request->get('password') != $request->request->get('passwordConfirm')) {
                $this->addFlash('danger', 'Le mot de passe que vous avez saisie ne correspond pas avec sa confirmation.');

                return $this->redirectToRoute('forgot_password');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

            if($user === null) {
                $this->addFlash('danger', 'Ce lien de réinitialisation n\'est plus valide.');
                return $this->redirectToRoute('forgot_password');
            }

            $user->setResetToken(null);
            $user->setPassword($userPasswordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();

            $this->addFlash('success', 'Le mot de passe de votre compte a été mis à jour avec succès. La modification est immédiate.');
            return $this->redirectToRoute('homepage');

        } else {
            return $this->render('security/forgot-password-reset.html.twig', ['token' => $token]);
        }
    }


}