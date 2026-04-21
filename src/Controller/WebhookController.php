<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\WebHook\AnimeWebhookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebhookController extends AbstractController
{
    #[Route('/webhook', name: 'app_webhook')]
    public function index(
        AnimeWebhookService $animeWebhookService,
        UserRepository $userRepository,
    ): Response
    {

        //$directory = $this->getParameter('kernel.project_dir') . '/public/webhook/Anime/';
        $files = glob($this->getParameter('kernel.project_dir').'/public/webhook/Anime/*.json');

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

                $library = str_replace("Quasinas ", "", $data['librarySectionTitle']);

                switch ($library) {
                case "Anime":
                    try {
                        $animeWebhookService->addAnime($data, $user);
                    }catch (\Exception $e){
                        dump($e);
                        dump($data);
                    }

                    break;
                case "Série":
                    break;
                default:
                    dump($data);
                }
            }
        }

        return new Response('OK');
    }
}
