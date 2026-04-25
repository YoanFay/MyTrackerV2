<?php

namespace App\Service\WebHook;

use App\Entity\Episode;
use App\Entity\EpisodeShow;
use App\Entity\Movie;
use App\Entity\MovieShow;
use App\Entity\Serie;
use App\Entity\SerieType;
use App\Repository\EpisodeRepository;
use App\Repository\MovieRepository;
use App\Repository\SerieRepository;
use App\Repository\SerieTypeRepository;
use App\Service\API\AniListService;
use App\Service\API\TMDBService;
use App\Service\StringService;
use App\Service\API\TVDBService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use JetBrains\PhpStorm\NoReturn;

class FilmWebhookService
{

    private MovieRepository $movieRepository;

    private TMDBService $TMDBService;

    private ObjectManager $manager;


    public function __construct(
        MovieRepository $movieRepository,
        TMDBService     $TMDBService,
        ManagerRegistry $managerRegistry,
    )
    {

        $this->movieRepository = $movieRepository;
        $this->TMDBService = $TMDBService;
        $this->manager = $managerRegistry->getManager();

    }


    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addMovie($data, $user): void
    {

        $plexId = str_replace("plex://movie/", "", $data['guid']);

        dump($data);

        $TMDBId = null;

        foreach ($data['Guid'] as $guid) {

            if (isset($guid['id']) && str_starts_with($guid['id'], 'tmdb://')) {
                $TMDBId = intval(str_replace(["tmdb://"], [""], $guid['id']));
                break;
            }

        }

        if ($TMDBId) {
            $movie = $this->movieRepository->findOneBy(['tmdbId' => $TMDBId]);
        } else {
            $movie = $this->movieRepository->findOneBy(['plexId' => $plexId]);
        }

        if (!$movie) {

            $movie = new Movie();
            $movie->setPlexId($plexId);
            $movie->setTmdbId($TMDBId);
            $movie->setName($data['title']);

            if ($TMDBId) {
                $this->TMDBService->updateMovieInfo($movie);
            }

            $this->manager->persist($movie);

        }

        $show = new MovieShow();
        $show->setUser($user);
        $show->setMovie($movie);
        $show->setShowDate($data['showDate']);

        $this->manager->persist($show);
        $this->manager->flush();

    }

}
