<?php

namespace App\Controller\Backoffice;

use App\Service\PrivateFilesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DownloadController extends AbstractController
{
    /**
     * @Route("/download/{file}/{userIdPath}", name="downloadResume")
     */
    public function downloadResume($file, $userIdPath, PrivateFilesService $privateFilesService)
    {
        if($file !== null) {
            return $privateFilesService->getResume($file, $userIdPath);
        }
    }
}
