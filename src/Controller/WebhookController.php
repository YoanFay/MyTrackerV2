<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebhookController extends AbstractController
{
    #[Route('/webhook', name: 'app_webhook')]
    public function index(): Response
    {

        $directory = $this->getParameter('kernel.project_dir') . '/public/webhook/';

        $filename = date('Y-m-d-H-i') . '.json';
        $payload = $_POST['payload'];
        file_put_contents($directory.$filename, $payload);

        return new Response('OK');
    }
}
