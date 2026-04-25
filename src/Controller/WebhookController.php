<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\WebHook\FilmWebhookService;
use App\Service\WebHook\MusiqueWebhookService;
use App\Service\WebHook\SerieWebhookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebhookController extends AbstractController
{
    #[Route('/webhook', name: 'app_webhook')]
    public function index(
        SerieWebhookService   $serieWebhookService,
        MusiqueWebhookService $musiqueWebhookService,
        FilmWebhookService    $filmWebhookService,
        UserRepository        $userRepository,
    ): Response
    {

        ini_set('max_execution_time', 0);

        $baseDir = $this->getParameter('kernel.project_dir').'/public/webhook';

        $files = array_merge(
        //glob($baseDir.'/Anime/*.json'),
        //glob($baseDir.'/Séries/*.json'),
        //glob($baseDir.'/Musique/*.json'),
            glob($baseDir.'/Films/*.json'),
        );

        foreach ($files as $file) {
            $string = file_get_contents($file);
            $json = json_decode($string, true);
            if ($json['event'] === "media.scrobble") {

                $plexName = $json["Account"]['title'];

                $user = $userRepository->findOneBy(['plexName' => $plexName]);

                if (!$user) {
                    return new Response("Pas OK");
                }

                $data = $json['Metadata'];

                // A SUPPRIME APRES TEST
                $data['showDate'] = \DateTime::createFromFormat('Y-m-d-H-i-s', str_replace('.json', '', basename($file)));

                $library = str_replace(['Quasinas ', ' A Deux', ' Chat', ' Doudou'], "", $data['librarySectionTitle']);

                switch ($library) {
                case "Anime":
                    try {
                        $serieWebhookService->addSerie($data, $user, true);
                    } catch (\Exception $e) {
                        dump($e);
                        dump($data);
                    }

                    break;
                case "Séries":
                    try {
                        $serieWebhookService->addSerie($data, $user);
                    } catch (\Exception $e) {
                        dump($e);
                        dump($data);
                    }

                    break;
                case "Musique":
                    try {
                        $musiqueWebhookService->addMusic($data, $user);
                    } catch (\Exception $e) {
                        dump($e);
                        dump($data);
                    }

                    break;
                case "Films":
                    try {
                        $filmWebhookService->addMovie($data, $user);
                    } catch (\Exception $e) {
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
