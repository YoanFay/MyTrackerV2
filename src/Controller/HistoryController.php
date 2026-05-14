<?php

namespace App\Controller;

use App\Repository\EpisodeShowRepository;
use App\Repository\MovieShowRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HistoryController extends AbstractController
{
    /**
     * @param EpisodeShowRepository $episodeShowRepository
     * @param MovieShowRepository   $movieShowRepository
     * @param int|null              $year
     * @param int|null              $month
     *
     * @return Response
     */
    #[Route('/history/{year}/{month}', name: 'history')]
    public function index
    (
        EpisodeShowRepository $episodeShowRepository,
        MovieShowRepository   $movieShowRepository,
        ?int                   $year = null,
        ?int                   $month = null,
    ): Response
    {

        $monthList = [
        'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre',

    ];

        $histories = [];

        $showTime = [
            'total' => 0,
            'anime' => 0,
            'series' => 0,
            'replay' => 0,
            'movie' => 0,
        ];

        if($year && $month){
            $episodeShows = $episodeShowRepository->getShowByDate($year.'-'.$month.'-'.'-01');
            $title = 'Historique du mois de '.$monthList[$month-1].' '.$year;
        }elseif($year){
            $episodeShows = $episodeShowRepository->getShowByDate($year.'-'.'-01-01');
            $title = 'Historique de '.$year;
        }else{
            $episodeShows = $episodeShowRepository->findAll();
            $title = 'Historique de visionnage global';
        }

        foreach ($episodeShows as $episodeShow) {

            $episode = $episodeShow->getEpisode();
            $serie = $episode->getSerie();

            if (!isset($showTime[$episodeShow->getShowDate()->format('Y/m/d')])) {
                $showTime[$episodeShow->getShowDate()->format('Y/m/d')] = [
                    'total' => 0,
                    'anime' => 0,
                    'series' => 0,
                    'replay' => 0,
                    'movie' => 0,
                ];
            }

            $showTime[$serie->getSerieType()->getSlug()] += $episode->getDuration();
            $showTime['total'] += $episode->getDuration();

            $showTime[$episodeShow->getShowDate()->format('Y/m/d')][$serie->getSerieType()->getSlug()] += $episode->getDuration();
            $showTime[$episodeShow->getShowDate()->format('Y/m/d')]['total'] += $episode->getDuration();

            $histories[] = [
                'serie' => $serie,
                'episode' => $episode,
                'date' => $episodeShow->getShowDate(),
                'type' => $serie->getSerieType()->getName(),
                'badge' => $serie->getSerieType()->getSlug(),
            ];
        }

        if($year && $month){
            $movieShows = $movieShowRepository->getShowByDate($year.'-'.$month.'-'.'-01');
        }elseif($year){
            $movieShows = $movieShowRepository->getShowByDate($year.'-'.'-01-01');
        }else{
            $movieShows = $movieShowRepository->findAll();
        }

        foreach ($movieShows as $movieShow) {

            $movie = $movieShow->getMovie();

            if (!isset($showTime[$movieShow->getShowDate()->format('Y/m/d')])) {
                $showTime[$movieShow->getShowDate()->format('Y/m/d')] = [
                    'total' => 0,
                    'anime' => 0,
                    'series' => 0,
                    'replay' => 0,
                    'movie' => 0,
                ];;
            }

            $showTime['movie'] += $movie->getDuration();
            $showTime['total'] += $movie->getDuration();

            $showTime[$movieShow->getShowDate()->format('Y/m/d')]['movie'] += $movie->getDuration();
            $showTime[$movieShow->getShowDate()->format('Y/m/d')]['total'] += $movie->getDuration();

            $histories[] = [
                'movie' => $movie,
                'date' => $movieShow->getShowDate(),
                'type' => 'Film',
                'badge' => 'movie',
            ];
        }

        usort($histories, function ($a, $b) {

            return $b['date'] <=> $a['date'];
        });

        return $this->render('history/index.html.twig', [
            'title' => $title,
            'histories' => $histories,
            'showTime' => $showTime
        ]);
    }
}
