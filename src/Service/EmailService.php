<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailService {
    private $entityManager;
    private $mailer;
    private $templating;

    public function __construct(EntityManagerInterface $entityManager, \Swift_Mailer $mailer, ContainerInterface $container) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->templating = $container->get('twig');
    }

    public function sendEmail($toEmail, $subject, $view, $viewData, $filePath = null) {

        $message = (new \Swift_Message($subject))
            ->setFrom('no-reply@jobandyou.fr', 'Job&You')
            ->setTo($toEmail)
            ->setBody(
                $this->templating->render(
                    $view, $viewData
                ),
                'text/html'
            );

        if($filePath && file_exists($filePath)) {
            $message->attach(\Swift_Attachment::fromPath($filePath));
        }

        $return = '';
        try {
            $this->mailer->send($message);
        } catch (\Exception $e) {
            $return = $e->getMessage();
        }
    }
}