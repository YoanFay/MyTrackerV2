<?php

namespace App\Service\API;

use App\Entity\AnimeGenre;
use App\Entity\AnimeTheme;
use App\Entity\InvolvedSerieCompany;
use App\Entity\Serie;
use App\Entity\SerieAnimeTheme;
use App\Entity\SerieCompany;
use App\Repository\AnimeGenreRepository;
use App\Repository\AnimeThemeRepository;
use App\Repository\InvolvedSerieCompanyRepository;
use App\Repository\SerieCompanyRepository;
use App\Service\StringService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\NoReturn;

class AniListService
{

    private SerieCompanyRepository $serieCompanyRepository;

    private AnimeGenreRepository $animeGenreRepository;

    private AnimeThemeRepository $animeThemeRepository;

    private InvolvedSerieCompanyRepository $involvedSerieCompanyRepository;

    private ObjectManager $manager;

    private StringService $stringService;

    private array $companyCache = [];

    private array $animeGenreCache = [];

    private array $animeThemeCache = [];


    public function __construct(
        SerieCompanyRepository         $serieCompanyRepository,
        AnimeGenreRepository           $animeGenreRepository,
        AnimeThemeRepository           $animeThemeRepository,
        InvolvedSerieCompanyRepository $involvedSerieCompanyRepository,
        StringService                  $stringService,
        ManagerRegistry                $managerRegistry,
    )
    {

        $this->serieCompanyRepository = $serieCompanyRepository;
        $this->animeGenreRepository = $animeGenreRepository;
        $this->animeThemeRepository = $animeThemeRepository;
        $this->involvedSerieCompanyRepository = $involvedSerieCompanyRepository;
        $this->stringService = $stringService;
        $this->manager = $managerRegistry->getManager();
    }


    /**
     * @param Serie $anime
     * @param int   $amount
     * @param int   $totalScore
     *
     * @return void
     */
    #[NoReturn] public function newAnime(Serie $anime, int $amount = 0, int $totalScore = 0): void
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { title{english}, status, relations{ edges{relationType}, nodes{title{english, romaji}} }, stats { scoreDistribution {score, amount}}, endDate{day, month, year}, startDate{day, month, year}, nextAiringEpisode{airingAt}, studios{edges{isMain},nodes{name}}, genres, tags{ name,rank, description, isMediaSpoiler,isGeneralSpoiler } }}';

        $searchName = $anime->getNameEng();

        if ($anime->getLastSeasonName()) {
            $searchName = $anime->getLastSeasonName();
        }

        $variables = [
            "search" => mb_convert_kana($searchName, 'a', 'UTF-8')
        ];

        $data = $this->request($query, $variables);

        if (!$anime->getName()) {
            $anime->setName($data['title']['english']);
            $anime->setSlug($this->stringService->slugify($anime->getName()));
            $anime->setIsVfName(false);
        }

        $startDate = $this->formattedDate($data['startDate']);

        if (!$anime->getFirstAired() || $anime->getFirstAired() > $startDate) {
            $anime->setFirstAired($startDate);
        }

        if ($data['endDate']['day']) {
            $anime->setLastAired($this->formattedDate($data['endDate']));
        }

        foreach ($data['studios']['nodes'] as $studioKey => $studio) {

            $this->studioTreatment($anime, $studio['name'], $data['studios']['edges'][$studioKey]['isMain']);

        }

        foreach ($data['genres'] as $genre){
            $this->genreTreatment($anime, $genre);
        }

        foreach ($data['tags'] as $theme){
            if($theme['rank'] >= 75){
                $this->themeTreatment($anime, $theme);
            }
        }

        foreach ($data['stats']['scoreDistribution'] as $score) {

            $amount += $score['amount'];
            $totalScore += $score['score'] * $score['amount'];

        }

        $newScore = round($totalScore / $amount, 0, PHP_ROUND_HALF_DOWN);

        $anime->setScore($newScore);

        $keySequel = array_search(
            "SEQUEL",
            array_column($data['relations']["edges"], 'relationType')
        );

        if ($keySequel !== false && $data['relations']["nodes"][$keySequel]["title"]['english'] != null) {
            $sequelName = $data['relations']["nodes"][$keySequel]["title"]['english'];

            $anime->setLastSeasonName($sequelName);

            $this->manager->persist($anime);
            $this->manager->flush();

            $this->newAnime($anime, $amount, $totalScore);
        } else {
            $anime->setLastSeasonName($data['title']['english']);

            $anime->setStatus($data['status']);

            if ($data['nextAiringEpisode'] && $data['nextAiringEpisode']['airingAt']) {
                $nextAiringEpisode = new DateTime();
                $nextAiringEpisode->setTimestamp($data['nextAiringEpisode']['airingAt']);
                $anime->setNextAired($nextAiringEpisode);

                if ($anime->getLastAired() < $anime->getNextAired()) {
                    $anime->setLastAired($anime->getNextAired());
                }
            }
        }
    }


    /**
     * @param string               $query
     * @param array<string, mixed> $variables
     *
     * @return mixed|null
     */
    public function request(string $query, array $variables): mixed
    {

        dump("REQUEST CALLED");

        $http = new Client([
            'timeout' => 10
        ]);

        $errorMax = 5;
        $error = 0;

        do {
            try {
                $response = $http->post('https://graphql.anilist.co', [
                    'json' => [
                        'query' => $query,
                        'variables' => $variables,
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                return $data['data']['Media'];
            } catch (GuzzleException $e) {
                $error++;
                dump($e->getMessage());
                dump("Erreur N°".$error." sur ".$errorMax);
                if ($errorMax !== $error) {
                    sleep(10 * $error);
                }
            }
        } while ($error < $errorMax);

        return false;

    }


    public function formattedDate($arrayDate): DateTime|null
    {

        if ($arrayDate['year'] && $arrayDate['month'] && $arrayDate['day']) {
            return DateTime::createFromFormat('Y-m-d', $arrayDate['year']."-".$arrayDate['month']."-".$arrayDate['day'])->setTime(0, 0);
        }

        return null;

    }


    public function studioTreatment($anime, $studioName, $isMain): void
    {

        if (isset($this->companyCache[$studioName])) {
            $serieCompany = $this->companyCache[$studioName];
        } else {
            $serieCompany = $this->serieCompanyRepository->findOneBy(['name' => $studioName]);

            if (!$serieCompany) {
                $serieCompany = new SerieCompany();
                $serieCompany->setName($studioName);
                $this->manager->persist($serieCompany);
            }

            $this->companyCache[$studioName] = $serieCompany;
        }

        if ($isMain) {
            $involved = $this->involvedSerieCompanyRepository->findOneBy(['isStudio' => true, 'serie' => $anime, 'company' => $serieCompany]);

            if (!$involved) {
                $involved = new InvolvedSerieCompany();
                $involved->setSerie($anime);
                $involved->setCompany($serieCompany);
                $involved->setIsStudio(true);
            }
        } else {
            $involved = $this->involvedSerieCompanyRepository->findOneBy(['isProducer' => true, 'serie' => $anime, 'company' => $serieCompany]);

            if (!$involved) {
                $involved = new InvolvedSerieCompany();
                $involved->setSerie($anime);
                $involved->setCompany($serieCompany);
                $involved->setIsProducer(true);
            }
        }

        $this->manager->persist($involved);

    }


    public function genreTreatment(Serie $anime, $genreName): void
    {

        if (isset($this->animeGenreCache[$genreName])) {
            $animeGenre = $this->animeGenreCache[$genreName];
        } else {
            $animeGenre = $this->animeGenreRepository->findOneBy(['nameEng' => $genreName]);

            if (!$animeGenre) {
                $animeGenre = new AnimeGenre();
                $animeGenre->setNameEng($genreName);

                $this->manager->persist($animeGenre);
            }

            $this->animeGenreCache[$genreName] = $animeGenre;
        }

        $anime->addAnimeGenre($animeGenre);

    }


    public function themeTreatment(Serie $anime, $dataTheme): void
    {

        $themeName = $dataTheme['name'];
        $level = $dataTheme['rank'];
        $description = $dataTheme['description'];

        if (isset($this->animeThemeCache[$themeName])) {
            $animeTheme = $this->animeThemeCache[$themeName];
        } else {
            $animeTheme = $this->animeThemeRepository->findOneBy(['nameEng' => $themeName]);

            if (!$animeTheme) {
                $animeTheme = new AnimeTheme();
                $animeTheme->setNameEng($themeName);
                $animeTheme->setLevel($level);
                $animeTheme->setDescriptionEng($description);

                $this->manager->persist($animeTheme);
            }

            $this->animeThemeCache[$themeName] = $animeTheme;
        }

        $serieAnimeTheme = new SerieAnimeTheme();
        $serieAnimeTheme->setSerie($anime);
        $serieAnimeTheme->setAnimeTheme($animeTheme);

        if($dataTheme['isMediaSpoiler'] || $dataTheme['isGeneralSpoiler']){
            $serieAnimeTheme->setIsSpoiler(true);
        }

        $this->manager->persist($serieAnimeTheme);

    }


    public function clearCache(): void
    {

        $this->companyCache = [];
        $this->animeGenreCache = [];
        $this->animeThemeCache = [];
    }

}
