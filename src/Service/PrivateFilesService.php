<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class PrivateFilesService {
    private $security;
    private $projectDir;
    private $authChecker;

    public function __construct(Security $security, $projectDir, AuthorizationCheckerInterface $authorizationChecker) {
        $this->security = $security;
        $this->projectDir = $projectDir;
        $this->authChecker = $authorizationChecker;
    }

    public function getResume($file, $userIdPath) {
        $file = str_replace('../', '', $file);

        if($this->security->getUser() !== null && ($this->security->getUser()->getId() == (int)$userIdPath || $this->authChecker->isGranted('ROLE_ENTREPRISE'))) {
            $file = urldecode($file);
            $privateDirectory = $this->projectDir.'/private/resumes/'.$userIdPath.'/'.$file;

            if(file_exists($privateDirectory)) {
                $response = new BinaryFileResponse($privateDirectory);
                $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

                if($mimeTypeGuesser->isSupported()) {
                    $response->headers->set('Content-Type', $mimeTypeGuesser->guess($privateDirectory));
                }

                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_INLINE,
                    pathinfo($privateDirectory)['basename']
                );

                return $response;
            } else {
                return new Response('Ce fichier n\'est plus disponible ou vous ne disposez pas des autorisations nécessaires pour le consulter.');
            }
        }
    }
}