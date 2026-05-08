<?php

namespace App\Service\WebHook;

use App\Entity\Movie;
use App\Entity\MovieShow;
use App\Repository\MovieRepository;
use App\Service\API\TMDBService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;

class MovieWebhookService
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
     * @throws GuzzleException
     * @throws InvalidArgumentException
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
        $show->setShowDate(new DateTime());

        $this->manager->persist($show);
        $this->manager->flush();

    }


    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function importMovie($data, $user): void
    {

        $plexId = $data[1];
        $TMDBId = $data[0];

        if (!$TMDBId) {
            return;
        }

        $param = ['tmdbId' => $TMDBId];

        if ($plexId) {
            $param += ['plexId' => $plexId];
        }

        $movie = $this->movieRepository->findOneBy($param);

        if (!$movie) {

            $movie = new Movie();
            $movie->setPlexId($plexId);
            $movie->setTmdbId($TMDBId);

            $this->TMDBService->updateMovieInfo($movie);

            $this->manager->persist($movie);

        }

        $show = new MovieShow();
        $show->setUser($user);
        $show->setMovie($movie);
        $show->setShowDate(DateTime::createFromFormat('Y-m-d H-i-s', $data[2]));

        $this->manager->persist($show);
        $this->manager->flush();

    }

}
