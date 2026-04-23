<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\WebHook\SerieWebhookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebhookController extends AbstractController
{
    #[Route('/webhook', name: 'app_webhook')]
    public function index(
        SerieWebhookService $serieWebhookService,
        UserRepository $userRepository,
    ): Response
    {

        //$directory = $this->getParameter('kernel.project_dir') . '/public/webhook/Anime/';

        $baseDir = $this->getParameter('kernel.project_dir').'/public/webhook';

        $files = array_merge(
            glob($baseDir.'/Anime/*.json'),
            glob($baseDir.'/Séries/*.json')
        );

        foreach ($files as $file) {
            $string = file_get_contents($file);
            $json = json_decode($string, true);
            if ($json['event'] === "media.scrobble") {

                $plexName = $json["Account"]['title'];

                $user = $userRepository->findOneBy(['plexName' => $plexName]);

                if (!$user){
                    return new Response("Pas OK");
                }

                $data = $json['Metadata'];

                // A SUPPRIME APRES TEST
                $data['showDate'] = \DateTime::createFromFormat('Y-m-d-H-i-s', str_replace('.json', '', basename($file)));

                $library = str_replace("Quasinas ", "", $data['librarySectionTitle']);

                switch ($library) {
                case "Anime":
                    try {
                        $serieWebhookService->addSerie($data, $user, true);
                    }catch (\Exception $e){
                        dump($e);
                        dump($data);
                    }

                    break;
                case "Séries":
                    try {
                        $serieWebhookService->addSerie($data, $user);
                    }catch (\Exception $e){
                        dump($e);
                        dump($data);
                    }

                    break;
                default:
                    dump($json);
                }
            }
        }

        return new Response('OK');
    }
}
