<?php

namespace App\Controller\Backoffice;

use App\Entity\Emplois;
use App\Entity\Entreprise;
use App\Entity\Notification;
use App\Entity\TypeContrat;
use App\Entity\TypeExperience;
use App\Entity\User;
use App\Service\CompressImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class EntrepriseController extends AbstractController
{
    /**
     * @Route("/dashboard/entreprise/mon-entreprise", name="dashboard_entreprise_profil")
     */
    public function dashboardEntrepriseProfil(Request $request, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->isMethod('POST')) {
            /*
             * Check requied fields
             */
            if($request->request->get('denomination') &&
                $request->request->get('email') &&
                $request->request->get('telephone') &&
                $request->request->get('adresse') &&
                $request->request->get('codePostal') &&
                $request->request->get('ville') &&
                $request->request->get('siret') &&
                $request->request->get('nafApe')
            ) {
                /*
                 * Check if this e-mail already exist
                 */
                $exist = $em->getRepository(User::class)->findOneBy(['email' => strtolower($request->request->get('email'))]);
                if(!filter_var($request->request->get('email'), FILTER_VALIDATE_EMAIL) || ($exist && $exist != $this->getUser())) {
                    $this->addFlash('danger', 'L\'adresse e-mail saisie semble invalide ou est déjà attribuée à un utilisateur.');
                } else {
                    if(!empty($request->request->get('password')) ||
                        !empty($request->request->get('newPassword')) ||
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
                    $entreprise = $this->getUser()->getEntreprise();
                    $entreprise
                        ->setDenomination($request->request->get('denomination'))
                        ->setNomMarque($request->request->get('nomMarque'))
                        ->setTelephone($request->request->get('telephone'))
                        ->setAdresse($request->request->get('adresse'))
                        ->setCodePostal($request->request->get('codePostal'))
                        ->setVille($request->request->get('ville'))
                        ->setSiret($request->request->get('siret'))
                        ->setNafApe($request->request->get('nafApe'))
                        ->setLogo('images/register-box/2.png')
                        ->setDescription($request->request->get('description'))
                        ->setSiteWeb($request->request->get('siteWeb'))
                        ->setReseauxSociaux($request->request->get('reseauxSociaux'))
                        ->setSecteursActivites($request->request->get('secteursActivites'));

                    $em->flush();

                    $this->addFlash('success', 'Les informations de votre entreprise ont été mise à jour avec succès.<br>Elles seront publiées dans quelques instants.');
                    return $this->redirectToRoute('dashboard_entreprise_profil');
                }
            } else {
                $this->addFlash('danger', 'Un champ requis n\'a pas été renseigné.');
            }
        }

        return $this->render('backoffice/entreprise/profil.html.twig', [
            'controller_name' => 'EntrepriseController',
            'entreprise' => $this->getUser()->getEntreprise()
        ]);
    }

    /**
     * @Route("/dashboard/entreprise/mes-offres-emploi", name="dashboard_entreprise_emplois")
     */
    public function dashboardEntrepriseEmplois()
    {
        $em = $this->getDoctrine()->getManager();
        $emplois = $em->getRepository(Emplois::class)->findBy(array('Entreprise' => $this->getUser()->getEntreprise()), array('dateAdd' => 'DESC'));

        return $this->render('backoffice/entreprise/emplois.html.twig', [
            'controller_name' => 'EntrepriseController',
            'emplois' => $emplois
        ]);
    }

    /**
     * @Route("/dashboard/entreprise/poster-un-job/{idEmploi}", defaults={"idEmploi"=0}, name="dashboard_entreprise_post_emplois")
     */
    public function dashboardEntreprisePostEmplois(Request $request, $idEmploi = 0)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->isMethod('POST')) {
            /*
             * Check requied fields
             */
            if($request->request->get('intitule') &&
                $request->request->get('ville') &&
                (int)$request->request->get('typeContrat') &&
                $request->request->get('description')
            ) {
                $valid = true;
                if(strlen($request->request->get('intitule')) > 255) {
                    $this->addFlash('danger', 'L\'intitulé ne peut pas dépasser 255 caractères.');
                    $valid = false;
                }
                if(strlen($request->request->get('ville')) > 255) {
                    $this->addFlash('danger', 'Le lieu de travail ne peut pas dépasser 255 caractères.');
                    $valid = false;
                }
                if(strlen($request->request->get('description')) < 100) {
                    $this->addFlash('danger', 'La description du job est inférieur à 100 caractères.');
                    $valid = false;
                }
                $typeEmplois = $em->getRepository(TypeContrat::class)->find((int)$request->request->get('typeContrat'));
                if((int)$request->request->get('typeContrat') <= 0 || !$typeEmplois) {
                    $this->addFlash('danger', 'Veuillez sélectionner un type de contrat.');
                    $valid = false;
                }
                $typeExperience = $em->getRepository(TypeExperience::class)->find((int)$request->request->get('typeExperience'));
                if($request->request->get('typeExperience') === null || !$typeExperience) {
                    $this->addFlash('danger', 'Veuillez sélectionner l\'expérience souhaité.');
                    $valid = false;
                }

                if($valid) {
                    $active = 2;
                    if($request->request->has('publish')) {
                        $active = 1;
                    }

                    $id = 0;
                    if((int)$idEmploi) {
                        $id = $idEmploi;
                    } elseif((int)$request->request->get('idEmploi')) {
                        $id = (int)$request->request->get('idEmploi');
                    }
                    if($id && $em->getRepository(Emplois::class)->find((int)$id)) {
                        $emploi = $em->getRepository(Emplois::class)->find((int)$id);
                    } else {
                        $emploi = new Emplois();
                    }

                    $emploi
                        ->setEntreprise($this->getUser()->getEntreprise())
                        ->setIntitule(trim($request->request->get('intitule')))
                        ->setTypeContrat($typeEmplois)
                        ->setSalaireBrut((float)$request->request->get('salaireBrut'))
                        ->setVille(trim($request->request->get('ville')))
                        ->setDescription($request->request->get('description'))
                        ->setResponsabilite($request->request->get('responsabilite'))
                        ->setEtude($request->request->get('etude'))
                        ->setTypeExperience($typeExperience)
                        ->setAvantage($request->request->get('avantage'))
                        ->setActive($active)
                        ->setCountViewed(($emploi->getCountViewed() ? $emploi->getCountViewed() : 0))
                        ->setDateAdd(new \DateTime())
                        ->setDateUpd(new \DateTime());

                    $em->persist($emploi);

                    $em->flush();
                    if($active == 1) {
                        $this->addFlash('success', 'L\'offre d\'emploi a bien été prise en compte.<br>Elle sera publiée dans quelques instants ...');
                    } elseif($active == 2) {
                        $this->addFlash('success', 'L\'offre d\'emploi a bien été enregistrée en tant que brouillon.<br>Pour continuer l\'édition ou mettre en ligne l\'offre, rendez-vous dans : "Mes offres d\'emploi".');
                    } else {
                        $this->addFlash('success', 'L\'offre d\'emploi a bien été enregistrée en tant que brouillon.<br>Pour avoir un aperçu de la page final, continuer l\'édition ou mettre en ligne l\'offre, rendez-vous dans : "Mes offres d\'emploi".');
                    }

                    if($request->request->has('open')) {
                        return $this->redirectToRoute('emploi', array('idEmploi' => $emploi->getId()));
                    }
                    return $this->redirectToRoute('dashboard_entreprise_emplois');
                }
            } else {
                $this->addFlash('danger', 'Un champ requis n\'a pas été renseigné.');
            }
        }

        $emploi = $request->request->all();
        if($idEmploi) {
            $emploi = $em->getRepository(Emplois::class)->find((int)$idEmploi);
            if(!$emploi) {
                $this->addFlash('warning', 'Cette offre d\'emploi n\'est plus disponible.');
                return $this->redirectToRoute('dashboard_entreprise_emplois');
            }
        }

        return $this->render('backoffice/entreprise/post-job.html.twig', [
            'emploi' => $emploi,
            'controller_name' => 'EntrepriseController',
            'entreprise' => $this->getUser()->getEntreprise(),
            'typeContrat' => $em->getRepository(TypeContrat::class)->findBy(array('active' => 1)),
            'typeExperience' => $em->getRepository(TypeExperience::class)->findBy(array('active' => 1))
        ]);
    }

    /**
     * @Route("/dashboard/entreprise/supprimer-un-job/{idEmploi}", defaults={"idEmploi"=0}, name="dashboard_entreprise_delete_emplois")
     */
    public function dashboardEntrepriseDeleteEmplois(Request $request, $idEmploi = 0)
    {
        if((int)$idEmploi) {
            $em = $this->getDoctrine()->getManager();
            $emploi = $em->getRepository(Emplois::class)->findOneBy(array('id' => (int)$idEmploi, 'Entreprise' => $this->getUser()->getEntreprise()));

            if($emploi) {
                $em->remove($emploi);

                $em->flush();

                $this->addFlash('success', 'Offre d\'emploi supprimée avec succès.');
            } else {
                $this->addFlash('danger', 'Cette offre d\'emploi n\'existe plus où vous ne disposez pas des autorisations nécessaires pour la supprimer.');
            }
        } else {
            $this->addFlash('warning', 'Veuillez préciser quelle offre d\'emploi doit être supprimée.');
        }

        return $this->redirectToRoute('dashboard_entreprise_emplois');
    }


    /**
     * @Route("/dashboard/entreprise/logo", name="dashboard_entreprise_logo")
     */
    public function dashboardEntrepriseLogo(Request $request, CompressImageService $compressImage)
    {
        $em = $this->getDoctrine()->getManager();
        $fs = new Filesystem();

        if($request->isMethod('POST') && $request->files->get('logoEntreprise')) {
            $file = $request->files->get('logoEntreprise');
            if(in_array($file->guessExtension(), array('gif', 'jpeg', 'jpg', 'png', 'svg', 'bmp'))) {

                $pathTmp = 'images/tmp';
                $finalPath = 'images/company';

                $result = $compressImage->compressImage($file, $pathTmp, $finalPath);

                if($result['path'] && $fs->exists($result['path'])) {
                    if($this->getUser()->getEntreprise()->getLogo() && $fs->exists($this->getUser()->getEntreprise()->getLogo())) {
                        $fs->remove($this->getUser()->getEntreprise()->getLogo());
    
                    }
                    $this->getUser()->getEntreprise()->setLogo($result['path']);

                    $em->flush();
                    
                    $this->addFlash('success', 'Votre logo a bien été mis à jour.');
                } else {
                    foreach($result['errors'] as $error) {
                        $this->addFlash('danger', $error);
                    }
                }

            } else {
                $this->addFlash('danger', 'Le logo sélectionné n\'est pas reconnu comme étant une image.');
            }
        }

        return $this->redirectToRoute('dashboard_entreprise_profil');
    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
