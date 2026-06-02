<?php

namespace App\Controller;

use App\Repository\EpisodeShowRepository;
use App\Repository\MovieShowRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

final class HistoryController extends AbstractController
{

    /**
     * @param EpisodeShowRepository $episodeShowRepository
     * @param MovieShowRepository   $movieShowRepository
     *
     * @return Response
     */
    #[Route('/history', name: 'history')]
    public function index
    (
        EpisodeShowRepository $episodeShowRepository,
        MovieShowRepository   $movieShowRepository,
    ): Response
    {

        $episodeShows = $episodeShowRepository->findAll();
        $movieShows = $movieShowRepository->findAll();

        $showTime = [
            'total' => 0,
            'anime' => 0,
            'series' => 0,
            'replay' => 0,
            'movie' => 0,
            'year' => [],
        ];

        foreach ($episodeShows as $episodeShow) {

            $episode = $episodeShow->getEpisode();
            $serie = $episode->getSerie();

            if (!isset($showTime['year'][$episodeShow->getShowDate()->format('Y')])) {
                $showTime['year'][$episodeShow->getShowDate()->format('Y')] = [
                    'total' => 0,
                    'anime' => 0,
                    'series' => 0,
                    'replay' => 0,
                    'movie' => 0,
                    'month' => [],
                ];
            }

            if (!isset($showTime['year'][$episodeShow->getShowDate()->format('Y')]['month'][$episodeShow->getShowDate()->format('n')])) {
                $showTime['year'][$episodeShow->getShowDate()->format('Y')]['month'][$episodeShow->getShowDate()->format('n')] = [
                    'total' => 0,
                    'anime' => 0,
                    'series' => 0,
                    'replay' => 0,
                    'movie' => 0,
                ];
            }

            $showTime[$serie->getSerieType()->getSlug()] += $episode->getDuration();
            $showTime['total'] += $episode->getDuration();

            $showTime['year'][$episodeShow->getShowDate()->format('Y')][$serie->getSerieType()->getSlug()] += $episode->getDuration();
            $showTime['year'][$episodeShow->getShowDate()->format('Y')]['total'] += $episode->getDuration();

            $showTime['year'][$episodeShow->getShowDate()->format('Y')]['month'][$episodeShow->getShowDate()->format('n')][$serie->getSerieType()->getSlug()] += $episode->getDuration();
            $showTime['year'][$episodeShow->getShowDate()->format('Y')]['month'][$episodeShow->getShowDate()->format('n')]['total'] += $episode->getDuration();

        }

        foreach ($movieShows as $movieShow) {

            $movie = $movieShow->getMovie();

            if (!isset($showTime['year'][$movieShow->getShowDate()->format('Y')])) {
                $showTime['year'][$movieShow->getShowDate()->format('Y')] = [
                    'total' => 0,
                    'anime' => 0,
                    'series' => 0,
                    'replay' => 0,
                    'movie' => 0,
                    'month' => [],
                ];
            }

            if (!isset($showTime['year'][$movieShow->getShowDate()->format('Y')]['month'][$movieShow->getShowDate()->format('n')])) {
                $showTime['year'][$movieShow->getShowDate()->format('Y')]['month'][$movieShow->getShowDate()->format('n')] = [
                    'total' => 0,
                    'anime' => 0,
                    'series' => 0,
                    'replay' => 0,
                    'movie' => 0,
                ];
            }

            $showTime['movie'] += $movie->getDuration();
            $showTime['total'] += $movie->getDuration();

            $showTime['year'][$movieShow->getShowDate()->format('Y')]['movie'] += $movie->getDuration();
            $showTime['year'][$movieShow->getShowDate()->format('Y')]['total'] += $movie->getDuration();

            $showTime['year'][$movieShow->getShowDate()->format('Y')]['month'][$movieShow->getShowDate()->format('n')]['movie'] += $movie->getDuration();
            $showTime['year'][$movieShow->getShowDate()->format('Y')]['month'][$movieShow->getShowDate()->format('n')]['total'] += $movie->getDuration();

        }

        return $this->render('history/index.html.twig', [
            'showTime' => $showTime
        ]);

    }


    /**
     * @param EpisodeShowRepository $episodeShowRepository
     * @param MovieShowRepository   $movieShowRepository
     * @param int|string            $year
     * @param int|null              $month
     *
     * @return Response
     */
    #[Route('/history/{year}/{month}', name: 'history_date')]
    public function historyDate
    (
        EpisodeShowRepository $episodeShowRepository,
        MovieShowRepository   $movieShowRepository,
        Request               $request,
        Session               $session,
        int|string            $year,
        ?int                  $month = null,
    ): Response
    {

        $session->set('backRouteDetails', $request->getUri());

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

        if ($year !== "all" && $month) {

            $startDate = $year.'-'.$month.'-01';
            $endDate = date("Y-m-t 23:59", strtotime($startDate));

            $episodeShows = $episodeShowRepository->getShowByDate($startDate, $endDate);
            $title = 'Historique du mois de '.$monthList[$month - 1].' '.$year;
        } else if ($year !== "all") {
            $episodeShows = $episodeShowRepository->getShowByDate($year.'-01-01', $year.'-12-31 23:59');
            $title = 'Historique de '.$year;
        } else {
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

        if ($year !== "all" && $month) {

            $startDate = $year.'-'.$month.'-01';
            $endDate = date("Y-m-t 23:59", strtotime($startDate));

            $movieShows = $movieShowRepository->getShowByDate($startDate, $endDate);
        } else if ($year !== "all") {
            $movieShows = $movieShowRepository->getShowByDate($year.'-01-01', $year.'-12-31 23:59');
        } else {
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

        return $this->render('history/date.html.twig', [
            'title' => $title,
            'histories' => $histories,
            'showTime' => $showTime
        ]);
    }
}
