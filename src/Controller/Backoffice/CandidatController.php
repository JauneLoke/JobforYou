<?php

namespace App\Controller\Backoffice;

use App\Entity\Candidature;
use App\Entity\Emplois;
use App\Entity\User;
use App\Entity\Candidat;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CandidatController extends AbstractController
{
    /**
     * @Route("/dashboard/candidat/mon-profil", name="dashboard_candidat_profil")
     */
    public function dashboardCandidatProfil(Request $request, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->isMethod('POST')) {
            /*
             * Check requied fields
             */
            if($request->request->get('nom') &&
                $request->request->get('prenom') &&
                $request->request->get('telephone') &&
                $request->request->get('ville') &&
                $request->request->get('code_postal') &&
                $request->request->get('description')
            ) {
                /*
                 * Check if this e-mail already exist
                 */
                $exist = $em->getRepository(User::class)->findOneBy(['email' => strtolower($request->request->get('email'))]);
                if(!filter_var($request->request->get('email'), FILTER_VALIDATE_EMAIL) || ($exist && $exist != $this->getUser())) {
                    $this->addFlash('danger', 'L\'adresse e-mail saisie semble invalide ou est déjà attribuée à un utilisateur.');
                } else {
                    if(!empty($request->request->get('password')) &&
                        !empty($request->request->get('newPassword')) &&
                        !empty($request->request->get('newPasswordConfirm'))) {
                        if(!empty($request->request->get('password')) && $userPasswordEncoder->isPasswordValid($this->getUser(), $request->request->get('password'))) {
                            if(!empty($request->request->get('newPassword')) && !empty($request->request->get('newPasswordConfirm'))) {
                                if($request->request->get('newPassword') == $request->request->get('newPasswordConfirm')) {
                                    $this->getUser()->setPassword($userPasswordEncoder->encodePassword($this->getUser(), $request->request->get('newPassword')));
                                } else {
                                    $this->addFlash('danger', 'Erreur lors de la modification du mot de passe : Le nouveau mot de passe n\'est pas identique à sa confirmation.');
                                }
                            } else {
                                $this->addFlash('danger', 'Erreur lors de la modification du mot de passe : Merci de saisir un nouveau mot de passe et de le confirmer.');
                            }
                        } else {
                            $this->addFlash('danger', 'Erreur lors de la modification du mot de passe : Le mot de passe actuel est incorrect.');
                        }
                    }

                    $this->getUser()->setEmail($request->request->get('email'));
                    $candidat = $this->getUser()->getCandidat();
                    $candidat
                        ->setNom($request->request->get('nom'))
                        ->setPrenom($request->request->get('prenom'))
                        ->setTelephone($request->request->get('telephone'))
                        ->setVille($request->request->get('ville'))
                        ->setCodePostal($request->request->get('code_postal'))
                        ->setDescription($request->request->get('description'));

                    $em->flush();

                    $this->addFlash('success', 'Les informations de votre profil ont été mise à jour avec succès.<br>Elles seront publiées dans quelques instants.');
                    return $this->redirectToRoute('dashboard_candidat_profil');
                }
            } else {
                $this->addFlash('danger', 'Un champ requis n\'a pas été renseigné.');
            }
        }

        return $this->render('backoffice/candidat/profil.html.twig', [
            'controller_name' => 'CandidatController',
            'candidat' => $this->getUser()->getCandidat(),
            'form_params' => $request->request->all()
        ]);
    }

    /**
     * @Route("/dashboard/candidat/uploadResume", name="dashboard_candidat_upload_resume")
     */
    public function dashboardCandidatUploadResume(Request $request)
    {
        if($request->isMethod('POST') && $request->files->get('resume')) {
            $em = $this->getDoctrine()->getManager();
            $fs = new Filesystem();

            $file = $request->files->get('resume');

            if(in_array($file->guessExtension(), array('pdf'))) {
                $finalPath = $this->get('parameter_bag')->get('kernel.project_dir').'/private/resumes/'.$this->getUser()->getId();
                $filename = md5(uniqid()).'.'.$file->guessExtension();

                if (filesize($file) <= '5242880') {
                    try {
                        if(!$fs->exists($finalPath)) {
                            $fs->mkdir($finalPath, 0775);
                            // $fs->chgrp($finalPath, 'www-data', true);
                        }
                        $file->move($finalPath, $filename);

                        if($fs->exists($finalPath.'/'.$filename)) {
                            if($this->getUser()->getCandidat()->getCv() && $fs->exists($finalPath.'/'.$this->getUser()->getCandidat()->getCv())) {
                                $fs->remove($finalPath.'/'.$this->getUser()->getCandidat()->getCv());

                            }
                            $this->getUser()->getCandidat()->setCv($filename);

                            $em->flush();
                        }

                        $this->addFlash('success', 'Votre CV a bien été chargé. Il est désormais visible par les recruteurs');
                    } catch (\Exception $e) {
                        $this->addFlash('warning', 'Un problème est survenue lors de l\'envoi de votre CV dans nos serveurs : '.$e->getMessage());
                    }
                } else {
                    $this->addFlash('danger', 'Votre fichier PDF doit être inférieur à 5Mo. Veuillez compresser votre PDF avant de le soumettre.');
                }
            } else {
                $this->addFlash('danger', 'Le CV sélectionné n\'est pas un PDF valide.');
            }

        }

        return $this->redirectToRoute('dashboard_candidat_post_competences');
    }

    /**
     * @Route("/dashboard/candidat/competences", name="dashboard_candidat_post_competences")
     */
    public function dashboardCandidatCompetences(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->isMethod('POST')) {
            /*
             * Check requied fields
             */
            if($request->request->get('diplome')&&
                $request->request->get('experience') &&
                $request->request->get('competence')
                // $request->request->get('site_web')
            ) {
                
                $valid = true;

                if(strlen($request->request->get('diplome')) > 201) {
                    $this->addFlash('danger', 'Le nom du diplôme ne peut pas dépasser 50 caractères.');
                    $valid = false;
                }
                if(strlen($request->request->get('experience')) > 201) {
                    $this->addFlash('danger', 'L\'experience ne peut pas dépasser 50 caractères.');
                    $valid = false;
                }
                if(strlen($request->request->get('competence')) > 201) {
                    $this->addFlash('danger', 'Chaque compétences ne peut pas dépasser 50 caractères.');
                    $valid = false;
                }

            } else {
                $valid = false;
                $this->addFlash('danger', 'Un champ requis n\'a pas été renseigné.');
            }
        
        if($valid == true) {
            $candidat = $this->getUser()->getCandidat();
            $candidat
                ->setEducation($request->request->get('diplome'))
                ->setExperience($request->request->get('experience'))
                ->setCompetence($request->request->get('competence'))
                ->setSiteWeb($request->request->get('site_web'));

            $em->persist($candidat);

            $em->flush();

            }
            
        }
        
        return $this->render('backoffice/candidat/competences.html.twig', [
            'controller_name' => 'CandidatController',
            'candidat' => $this->getUser()->getCandidat(),
        ]);
    }

    /**
     * @Route("/dashboard/mescandidatures", name="dashboard_candidat_candidatures")
     */
    public function dashboardCandidatCandidatures () 
    {   
        $em = $this->getDoctrine()->getManager();

        $candidatures = $em->getRepository(Candidature::class)->findBy(array('Candidat' => $this->getUser()->getCandidat()));

        return $this->render('backoffice/candidat/candidatures.html.twig', [
            'controller_name' => 'CandidatController',
            'candidatures' => $candidatures,
        ]);
    }
}