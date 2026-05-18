<?php

namespace App\Service\WebHook;

use App\Entity\Episode;
use App\Entity\EpisodeShow;
use App\Entity\Serie;
use App\Entity\SerieType;
use App\Entity\User;
use App\Repository\EpisodeRepository;
use App\Repository\SerieRepository;
use App\Repository\SerieTypeRepository;
use App\Service\API\AniListService;
use App\Service\StringService;
use App\Service\API\TVDBService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\NoReturn;
use Psr\Cache\InvalidArgumentException;

class SerieWebhookService
{

    private SerieRepository $serieRepository;

    private EpisodeRepository $episodeRepository;

    private StringService $stringService;

    private SerieTypeRepository $serieTypeRepository;

    private TVDBService $TVDBService;

    private AniListService $aniListService;

    private ObjectManager $manager;

    private ManagerRegistry $managerRegistry;


    public function __construct(
        SerieRepository     $serieRepository,
        EpisodeRepository   $episodeRepository,
        StringService       $stringService,
        SerieTypeRepository $serieTypeRepository,
        TVDBService         $TVDBService,
        AniListService      $aniListService,
        ManagerRegistry     $managerRegistry,
    )
    {

        $this->serieRepository = $serieRepository;
        $this->episodeRepository = $episodeRepository;
        $this->stringService = $stringService;
        $this->serieTypeRepository = $serieTypeRepository;
        $this->TVDBService = $TVDBService;
        $this->aniListService = $aniListService;
        $this->manager = $managerRegistry->getManager();
        $this->managerRegistry = $managerRegistry;

    }


    /**
     * @param array<string, mixed> $data
     * @param User  $user
     * @param bool  $isAnime
     * @param bool  $isReplay
     *
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function addSerie(array $data, User $user, bool $isAnime = false, bool $isReplay = false): void
    {

        if ($isAnime) {

            if (!$serieType = $this->serieTypeRepository->findOneBy(['name' => 'Anime'])) {
                $serieType = $this->addAnimeType();
            }

        } else if ($isReplay) {

            if (!$serieType = $this->serieTypeRepository->findOneBy(['name' => 'Replay'])) {
                $serieType = $this->addReplayType();
            }

        } else {

            if (!$serieType = $this->serieTypeRepository->findOneBy(['name' => 'Séries'])) {
                $serieType = $this->addSerieType();
            }

        }


        $episodeTVDBId = null;

        foreach ($data['Guid'] as $guid) {

            if (isset($guid['id']) && str_starts_with($guid['id'], 'tvdb://')) {
                $episodeTVDBId = intval(str_replace(["tvdb://"], [""], $guid['id']));
                break;
            }

        }

        $seriePlexId = str_replace("plex://show/", "", $data['grandparentGuid']);
        $episodePlexId = str_replace("plex://episode/", "", $data['guid']);

        $serie = $this->isSerieExist($seriePlexId, $episodeTVDBId);

        if (!$serie) {

            $serie = new Serie();
            $serie->setName($data['grandparentTitle']);
            $serie->setPlexId($seriePlexId);
            $serie->setSlug($this->stringService->slugify($data['grandparentTitle']));
            $serie->setSerieType($serieType);

        }

        if (!$episode = $this->isEpisodeExist($serie, $data['parentIndex'], $data['index'])) {

            $episode = new Episode();
            $episode->setSerie($serie);
            $episode->setPlexId($episodePlexId);
            $episode->setTvdbId($episodeTVDBId);

            if ($episode->getTvdbId()) {

                $this->TVDBService->createEpisode($episode);

                if (!$serie->getTvdbId() || !$serie->isVfName()) {
                    if ($tvdbAnimeId = $this->TVDBService->getSerieIdByEpisodeId($episode->getTvdbId())) {
                        $serie->setTvdbId($tvdbAnimeId);
                        $this->TVDBService->updateSerieInfo($serie);


                        if ($isAnime) {

                            $this->aniListService->newAnime($serie);
                        } else {

                            $this->TVDBService->newSerie($serie);
                        }
                    }
                }

            } else {

                $episode->setName($data['title']);
                $episode->setSeasonNumber($data['parentIndex']);
                $episode->setEpisodeNumber($data['index']);
                $episode->setDuration($data['duration'] ?? null);

            }

        }

        $this->manager->persist($serie);
        $this->manager->persist($episode);

        $episodeShow = new EpisodeShow();
        $episodeShow->setUser($user);
        $episodeShow->setEpisode($episode);
        $episodeShow->setShowDate(new DateTime());

        $this->manager->persist($episodeShow);

        $this->manager->flush();

    }


    private function addAnimeType(): SerieType
    {

        $serieType = new SerieType();
        $serieType->setName("Anime");
        $serieType->setSlug("anime");
        $this->manager->persist($serieType);
        $this->manager->flush();

        return $serieType;
    }


    private function addReplayType(): SerieType
    {

        $serieType = new SerieType();
        $serieType->setName("Replay");
        $serieType->setSlug("replay");
        $this->manager->persist($serieType);
        $this->manager->flush();

        return $serieType;
    }


    private function addSerieType(): SerieType
    {

        $serieType = new SerieType();
        $serieType->setName("Séries");
        $serieType->setSlug("series");
        $this->manager->persist($serieType);
        $this->manager->flush();

        return $serieType;
    }


    /**
     * @param string      $plexId
     * @param int|null    $episodeTVDBId
     * @param string|null $serieTVDBId
     *
     * @return Serie|false
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    private function isSerieExist(string $plexId, ?int $episodeTVDBId, ?string $serieTVDBId = null): Serie|false
    {

        /** @var Serie|null $serie */
        $serie = $this->serieRepository->findByPlexOrTvdbId($plexId, $serieTVDBId);

        if ($serie) {
            return $serie;
        }

        if ($episodeTVDBId) {

            if ($tvdbAnimeId = $this->TVDBService->getSerieIdByEpisodeId($episodeTVDBId)) {

                /** @var Serie|null $serie */
                $serie = $this->serieRepository->findOneBy(['tvdbId' => $tvdbAnimeId]);

                if($serie){
                    $serie->setTVDBId($tvdbAnimeId);

                    return $serie;
                }

            }

        }

        return false;

    }


    /**
     * @param Serie $anime
     * @param int   $seasonNumber
     * @param int   $episodeNumber
     *
     * @return Episode|false
     */
    private function isEpisodeExist(Serie $anime, int $seasonNumber, int $episodeNumber): Episode|false
    {


        $episode = $this->episodeRepository->findOneBy(['serie' => $anime, 'seasonNumber' => $seasonNumber, 'episodeNumber' => $episodeNumber]);

        if ($episode) {
            return $episode;
        }

        return false;

    }


    /**
     * @param array<int, mixed> $data
     * @param User              $user
     *
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function importSerie(array $data, User $user): void
    {

        $user = $this->manager->find(User::class, $user->getId());

        $episodeTVDBId = $data[0];
        $episodePlexId = $data[1];
        $episodeSeasonNumber = $data[2];
        $episodeNumber = $data[3];
        $serieTVDBId = $data[4];
        $seriePlexId = $data[5];
        $serieType = $data[6];
        $showDate = $data[7];

        if ($serieType === "Anime") {

            if (!$serieType = $this->serieTypeRepository->findOneBy(['name' => 'Anime'])) {
                $serieType = $this->addAnimeType();
            }

        } else if ($serieType === "Replay") {

            if (!$serieType = $this->serieTypeRepository->findOneBy(['name' => 'Replay'])) {
                $serieType = $this->addReplayType();
            }

        } else if ($serieType === "Séries") {

            if (!$serieType = $this->serieTypeRepository->findOneBy(['name' => 'Séries'])) {
                $serieType = $this->addSerieType();
            }

        }

        if (!$serie = $this->isSerieExist($seriePlexId, $episodeTVDBId, $serieTVDBId)) {

            $serie = new Serie();
            $serie->setPlexId($seriePlexId);
            $serie->setSerieType($serieType);
            $serie->setTvdbId($serieTVDBId);

            $this->TVDBService->updateSerieInfo($serie);


            if ($serieType->getName() === "Anime") {

                $this->aniListService->newAnime($serie);
            } else {

                $this->TVDBService->newSerie($serie);
            }

            $this->manager->persist($serie);

        }

        if (!$episode = $this->isEpisodeExist($serie, $episodeSeasonNumber, $episodeNumber)) {

            $episode = new Episode();
            $episode->setSerie($serie);
            $episode->setPlexId($episodePlexId);
            $episode->setTvdbId($episodeTVDBId);
            $episode->setEpisodeNumber($episodeNumber);
            $episode->setSeasonNumber($episodeSeasonNumber);

            if ($episode->getTvdbId()) {

                $this->TVDBService->createEpisode($episode);

            }

            $this->manager->persist($episode);

        }

        $episodeShow = new EpisodeShow();
        $episodeShow->setUser($user);
        $episodeShow->setEpisode($episode);
        $episodeShow->setShowDate(DateTime::createFromFormat('Y-m-d H-i-s', $showDate));

        $this->manager->persist($episodeShow);
        $this->manager->flush();


    }


    // SerieWebhookService.php
    public function clearCaches(): void
    {

        // Réinitialiser l'ObjectManager avec le nouveau après reset
        $this->manager = $this->managerRegistry->getManager();

        // Propager aux services dépendants
        $this->aniListService->clearCache();
        $this->TVDBService->clearCache(); // si TVDBService a aussi des caches
    }

}
