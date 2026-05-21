<?php

namespace App\Controller;

use App\Repository\SerieRepository;
use App\Repository\SerieUpdateRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index
    (
        SerieRepository $serieRepository,
        SerieUpdateRepository $serieUpdateRepository,
    ): Response
    {

        $series = $serieRepository->findBy(['nextAired' => new DateTime()]);

        $serieUpdates = $serieUpdateRepository->findBy(['updateDate' => new DateTime()]);

        return $this->render('index.html.twig', [
            'series' => $series,
            'serieUpdates' => $serieUpdates,
        ]);
    }
}
