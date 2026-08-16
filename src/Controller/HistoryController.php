<?php

namespace App\Controller;

use App\Entity\EpisodeShow;
use App\Entity\MovieShow;
use App\Entity\User;
use App\Form\HistoryAddType;
use App\Repository\EpisodeShowRepository;
use App\Repository\MovieShowRepository;
use App\Repository\SerieTypeRepository;
use App\Service\WebHook\SerieWebhookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

final class HistoryController extends AbstractController
{

    private const MONTHS = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre',
    ];


    /**
     * @param EpisodeShowRepository $episodeShowRepository
     * @param MovieShowRepository   $movieShowRepository
     *
     * @return Response
     */
    #[Route('/history', name: 'history_menu')]
    public function index
    (
        EpisodeShowRepository $episodeShowRepository,
        MovieShowRepository   $movieShowRepository,
    ): Response
    {

        /** @var User $user */
        $user = $this->getUser();

        $episodeShows = $episodeShowRepository->findBy(['user' => $user]);
        $movieShows = $movieShowRepository->findBy(['user' => $user]);

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
     * @param Request               $request
     * @param Session               $session
     * @param int|string            $year
     * @param int|null              $month
     *
     * @return Response
     */
    #[Route('/history/{year}/{month}', name: 'history', requirements: ['year' => '\d+|all'])]
    public function history
    (
        EpisodeShowRepository $episodeShowRepository,
        MovieShowRepository   $movieShowRepository,
        Request               $request,
        Session               $session,
        int|string            $year,
        ?int                  $month = null,
    ): Response
    {

        /** @var User $user */
        $user = $this->getUser();

        $session->set('backRouteDetails', $request->getUri());

        [$start, $end, $title] = $this->resolveDateRange($year, $month);
        $histories = [];
        $showTime = $this->newDayBucket() + ['total' => 0];

        $movieShows = $start
            ?
            $movieShowRepository->getShowByDate($start, $end, $user)
            :
            $movieShowRepository->findBy(['user' => $user]);

        foreach ($movieShows as $movieShow) {
            $this->addMovieShow($showTime, $histories, $movieShow);
        }

        $episodeShows = $start
            ?
            $episodeShowRepository->getShowByDate($start, $end, $user)
            :
            $episodeShowRepository->getShowAll($user);

        foreach ($episodeShows as $episodeShow) {
            $this->addEpisodeShow($showTime, $histories, $episodeShow);
        }

        usort($histories, function ($a, $b) {

            return $b['date'] <=> $a['date'];
        });

        return $this->render('history/date.html.twig', [
            'title' => $title,
            'histories' => $histories,
            'showTime' => $showTime,
            'year' => $year,
            'month' => $month,
        ]);
    }


    /**
     * @param int|string  $year
     * @param int|null    $month
     * @param string|null $type
     *
     * @return array{0: string|null, 1: string|null, 2: string}
     */
    private function resolveDateRange(int|string $year, ?int $month, ?string $type = null): array
    {

        $tilteType = match ($type) {
            'anime' => "des animes",
            'series' => "des séries",
            'replay' => "des replay",
            'movie' => "des films",
            default => "",
        };

        if ($year === 'all') {
            return [null, null, 'Historique '.$tilteType.' de visionnage global'];
        }

        if ($month) {
            $start = "$year-$month-01";
            $end = date("Y-m-t 23:59", strtotime($start));

            $monthName = self::MONTHS[$month - 1];
            $prefix = preg_match('/^[aeiouyhAEIOUYH]/', $monthName) ?
                "d'" :
                'de ';
            $title = 'Historique '.$tilteType.' du mois '.$prefix.$monthName.' '.$year;

        } else {
            $start = "$year-01-01";
            $end = "$year-12-31 23:59";
            $title = 'Historique '.$tilteType.' '.$year;
        }

        return [$start, $end, $title];
    }


    /**
     * @return array<string, int>
     */
    private function newDayBucket(): array
    {

        return ['total' => 0, 'anime' => 0, 'series' => 0, 'replay' => 0, 'movie' => 0];
    }


    /**
     * @param array<string, mixed>             $showTime
     * @param-out array<string, mixed> $showTime
     * @param array<int, array<string, mixed>> $histories
     * @param MovieShow                        $movieShow
     *
     * @return void
     */
    private function addMovieShow(array &$showTime, array &$histories, MovieShow $movieShow): void
    {

        $movie = $movieShow->getMovie();
        $day = $movieShow->getShowDate()->format('Y/m/d');

        $showTime[$day] ??= $this->newDayBucket();

        $showTime['movie'] += $movie->getDuration();
        $showTime['total'] += $movie->getDuration();
        $showTime[$day]['movie'] += $movie->getDuration();
        $showTime[$day]['total'] += $movie->getDuration();

        $histories[] = [
            'movie' => $movie,
            'date' => $movieShow->getShowDate(),
            'type' => 'Film',
            'badge' => 'movie',
        ];
    }


    /**
     * @param array<string, mixed>             $showTime
     * @param-out array<string, mixed> $showTime
     * @param array<int, array<string, mixed>> $histories
     * @param EpisodeShow                      $episodeShow
     *
     * @return void
     */
    private function addEpisodeShow(array &$showTime, array &$histories, EpisodeShow $episodeShow): void
    {

        $episode = $episodeShow->getEpisode();
        $serie = $episode->getSerie();
        $slug = $serie->getSerieType()->getSlug();
        $day = $episodeShow->getShowDate()->format('Y/m/d');

        $showTime[$day] ??= $this->newDayBucket();

        $showTime[$slug] += $episode->getDuration();
        $showTime['total'] += $episode->getDuration();
        $showTime[$day][$slug] += $episode->getDuration();
        $showTime[$day]['total'] += $episode->getDuration();

        $histories[] = [
            'serie' => $serie,
            'episode' => $episode,
            'date' => $episodeShow->getShowDate(),
            'type' => $serie->getSerieType()->getName(),
            'badge' => $slug,
        ];
    }


    /**
     * @param EpisodeShowRepository $episodeShowRepository
     * @param MovieShowRepository   $movieShowRepository
     * @param SerieTypeRepository   $serieTypeRepository
     * @param Request               $request
     * @param Session               $session
     * @param string                $type
     * @param int|string            $year
     * @param int|null              $month
     *
     * @return Response
     */
    #[Route('/history/{type}/{year}/{month}', name: 'history_type', requirements: ['type' => 'anime|series|replay|movie', 'year' => '\d+|all'])]
    public function history_type
    (
        EpisodeShowRepository $episodeShowRepository,
        MovieShowRepository   $movieShowRepository,
        SerieTypeRepository   $serieTypeRepository,
        Request               $request,
        Session               $session,
        string                $type,
        int|string            $year,
        ?int                  $month = null,
    ): Response
    {

        /** @var User $user */
        $user = $this->getUser();

        $session->set('backRouteDetails', $request->getUri());

        [$start, $end, $title] = $this->resolveDateRange($year, $month, $type);
        $histories = [];
        $showTime = $this->newDayBucket() + ['total' => 0];

        if ($type === 'movie') {
            $movieShows = $start
                ?
                $movieShowRepository->getShowByDate($start, $end, $user)
                :
                $movieShowRepository->findBy(['user' => $user]);

            foreach ($movieShows as $movieShow) {
                $this->addMovieShow($showTime, $histories, $movieShow);
            }
        } else {
            $typeNames = ['anime' => 'Anime', 'series' => 'Séries', 'replay' => 'Replay'];
            $serieType = $serieTypeRepository->findOneBy(['name' => $typeNames[$type]]);

            $episodeShows = $start
                ?
                $episodeShowRepository->getShowByDate($start, $end, $user, $serieType)
                :
                $episodeShowRepository->getShowAll($user, $serieType);

            foreach ($episodeShows as $episodeShow) {
                $this->addEpisodeShow($showTime, $histories, $episodeShow);
            }
        }

        usort($histories, function ($a, $b) {

            return $b['date'] <=> $a['date'];
        });

        return $this->render('history/date.html.twig', [
            'title' => $title,
            'histories' => $histories,
            'showTime' => $showTime,
            'type' => $type,
            'year' => $year,
            'month' => $month,
        ]);
    }


    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    #[Route('/history/add/{type}', name: 'history_add', requirements: ['type' => 'anime|series|replay|movie'])]
    public function history_add(
        SerieWebhookService $serieWebhookService,
        Request $request,
        string $type
    ): Response
    {

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createFormBuilder(['entries' => []])
            ->add('entries', CollectionType::class, [
                'entry_type' => HistoryAddType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'label' => false,
                'entry_options' => [
                    'attr' => [
                        'class' => 'd-flex flex-row justify-content-around',
                    ],
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ( $data['entries'] as $entry){
                $serieWebhookService->importSerieById($entry, $type, $user);
            }

            return $this->redirectToRoute('history_type', ['type' => $type, 'year' => 'all']);
        }

        return $this->render('history/add.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }
}
