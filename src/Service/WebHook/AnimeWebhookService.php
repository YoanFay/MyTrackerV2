<?php

namespace App\Service\WebHook;

use App\Entity\Episode;
use App\Entity\EpisodeShow;
use App\Entity\Serie;
use App\Entity\SerieType;
use App\Repository\EpisodeRepository;
use App\Repository\SerieRepository;
use App\Repository\SerieTypeRepository;
use App\Service\API\AniListService;
use App\Service\StringService;
use App\Service\API\TVDBService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use JetBrains\PhpStorm\NoReturn;

class AnimeWebhookService
{

    private SerieRepository $serieRepository;

    private EpisodeRepository $episodeRepository;

    private StringService $stringService;

    private SerieTypeRepository $serieTypeRepository;

    private TVDBService $TVDBService;

    private AniListService $aniListService;

    private ObjectManager $manager;


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

    }


    public function addAnime($data, $user): void
    {

        if (!$serieType = $this->serieTypeRepository->findOneBy(['name' => 'Anime'])) {
            $serieType = $this->addAnimeType();
        }

        $episodeTVDBId = null;

        foreach ($data['Guid'] as $guid) {

            if (isset($guid['id']) && str_starts_with($guid['id'], 'tvdb://')) {
                $episodeTVDBId = intval(str_replace(["tvdb://"], [""], $guid['id']));
                break;
            }

        }

        $animePlexId = str_replace("plex://show/", "", $data['grandparentGuid']);
        $episodePlexId = str_replace("plex://episode/", "", $data['guid']);

        if (!$anime = $this->isSerieExist($animePlexId, $episodeTVDBId)) {

            $anime = new Serie();
            $anime->setName($data['grandparentTitle']);
            $anime->setPlexId($animePlexId);
            $anime->setSlug($this->stringService->slugify($data['grandparentTitle']));
            $anime->setSerieType($serieType);

        }

        if (!$episode = $this->isEpisodeExist($anime, $data['parentIndex'], $data['index'])) {

            $episode = new Episode();
            $episode->setSerie($anime);
            $episode->setPlexId($episodePlexId);
            $episode->setTvdbId($episodeTVDBId);

            if ($episode->getTvdbId()) {

                $this->TVDBService->createEpisode($episode);

                if (!$anime->getTvdbId() || !$anime->isVfName()) {
                    if ($tvdbAnimeId = $this->TVDBService->getSerieIdByEpisodeId($episode->getTvdbId())) {
                        $anime->setTvdbId($tvdbAnimeId);
                        $this->TVDBService->updateSerieInfo($anime);

                        $this->animeDataUpdate($anime);
                    }
                }

            } else {

                $episode->setName($data['title']);
                $episode->setSeasonNumber($data['parentIndex']);
                $episode->setEpisodeNumber($data['index']);
                $episode->setDuration($data['duration'] ?? null);

            }

            dump($anime->getName()." : Saison ".$episode->getSeasonNumber()." - Episode ".$episode->getEpisodeNumber());

        }

        $this->manager->persist($anime);
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


    private function isSerieExist($plexId, $episodeTVDBId): bool|object
    {

        $anime = $this->serieRepository->findOneBy(['plexId' => $plexId]);

        if ($anime) {
            return $anime;
        }

        if ($episodeTVDBId) {

            if ($tvdbAnimeId = $this->TVDBService->getSerieIdByEpisodeId($episodeTVDBId)) {

                /** @var Serie $anime */
                $anime = $this->serieRepository->findOneBy(['tvdbId' => $tvdbAnimeId]);

                if ($anime) {
                    $anime->setTVDBId($plexId);
                    return $anime;
                }

            }

        }

        return false;

    }


    private function isEpisodeExist($anime, $seasonNumber, $episodeNumber): bool|object
    {


        $episode = $this->episodeRepository->findOneBy(['serie' => $anime, 'seasonNumber' => $seasonNumber, 'episodeNumber' => $episodeNumber]);

        if ($episode) {
            return $episode;
        }

        return false;

    }


    #[NoReturn] private function animeDataUpdate($anime): void
    {

        $this->aniListService->newAnime($anime);

    }

}
