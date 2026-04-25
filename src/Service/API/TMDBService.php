<?php

namespace App\Service\API;

use App\Entity\Movie;
use App\Entity\Serie;
use App\Entity\TMDBGenre;
use App\Repository\TMDBGenreRepository;
use App\Service\ImageService;
use App\Service\StringService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpKernel\KernelInterface;

class TMDBService
{
    private ObjectManager $manager;

    private string $tmdbToken;

    private StringService $stringService;

    private ImageService $imageService;

    private TMDBGenreRepository $TMDBGenreRepository;

    private array $genreCache = [];


    public function __construct(
        ManagerRegistry     $managerRegistry,
        string              $tmdbToken,
        StringService       $stringService,
        ImageService        $imageService,
        TMDBGenreRepository $TMDBGenreRepository,
    )
    {

        $this->manager = $managerRegistry->getManager();
        $this->tmdbToken = $tmdbToken;
        $this->stringService = $stringService;
        $this->imageService = $imageService;
        $this->TMDBGenreRepository = $TMDBGenreRepository;

    }


    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function updateMovieInfo(Movie $movie): void
    {

        $data = self::getData($movie->getTmdbId());

        if ($data) {
            $movie->setName($data['title']);
            $movie->setSlug($this->stringService->slugify($data['title']));
            $movie->setDuration($data['runtime'] * 60000);
            $movie->setReleaseDate(\DateTime::createFromFormat('Y-m-d', $data['release_date'])->setTime(0, 0));
            $movie->setUpdated(true);

            foreach ($data['genres'] as $genre) {
                self::genreTreatment($movie, $genre);
            }

            self::updateArtwork($movie, $data['poster_path']);
        }

    }


    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function getData($id)
    {

        $client = new Client();

        $token = $this->tmdbToken;

        try {
            $response = $client->get("https://api.themoviedb.org/3/movie/".$id."?language=fr-FR", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            $data = null;
        }

        return $data;
    }


    public function updateArtwork(Movie $movie, $path): void
    {

        $this->imageService->addImage("movie/poster/", $movie->getSlug(), "https://image.tmdb.org/t/p/w500".$path);

    }


    public function genreTreatment($movie, $genre): void
    {

        if (isset($this->genreCache[$genre['name']])) {
            $tmdbGenre = $this->genreCache[$genre['name']];
        } else {
            $tmdbGenre = $this->TMDBGenreRepository->findOneBy(['nameFra' => $genre['name']]);

            if (!$tmdbGenre) {
                $tmdbGenre = new TMDBGenre();
                $tmdbGenre->setNameFra($genre['name']);
                $this->manager->persist($tmdbGenre);
            }

            $this->genreCache[$genre['name']] = $tmdbGenre;
        }

        if (!$tmdbGenre->hasMovie($movie)) {
            $tmdbGenre->addMovie($movie);
            $this->manager->persist($tmdbGenre);
        }

    }

}
