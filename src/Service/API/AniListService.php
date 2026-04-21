<?php

namespace App\Service\API;

use App\Entity\InvolvedSerieCompany;
use App\Entity\Serie;
use App\Entity\SerieCompany;
use App\Repository\InvolvedSerieCompanyRepository;
use App\Repository\SerieCompanyRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\NoReturn;

class AniListService
{

    private SerieCompanyRepository $serieCompanyRepository;

    private InvolvedSerieCompanyRepository $involvedSerieCompanyRepository;

    private ObjectManager $manager;

    private array $companyCache = [];


    public function __construct(
        SerieCompanyRepository         $serieCompanyRepository,
        InvolvedSerieCompanyRepository $involvedSerieCompanyRepository,
        ManagerRegistry     $managerRegistry,
    )
    {

        $this->serieCompanyRepository = $serieCompanyRepository;
        $this->involvedSerieCompanyRepository = $involvedSerieCompanyRepository;
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

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { title{english}, status, relations{ edges{relationType}, nodes{title{english, romaji}} }, stats { scoreDistribution {score, amount}}, endDate{day, month, year}, startDate{day, month, year}, nextAiringEpisode{airingAt}, studios{edges{isMain},nodes{name}} }}';

        $searchName = $anime->getNameEng();

        if ($anime->getLastSeasonName()) {
            $searchName = $anime->getLastSeasonName();
        }

        $variables = [
            "search" => mb_convert_kana($searchName, 'a', 'UTF-8')
        ];

        $data = $this->request($query, $variables);

        dump($data);

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

        if ($keySequel !== false) {
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
                sleep(5 * $error);
            }
        } while ($error < $errorMax);

        return false;

    }


    public function formattedDate($arrayDate): DateTime|bool
    {

        return DateTime::createFromFormat('Y-m-d', $arrayDate['year']."-".$arrayDate['month']."-".$arrayDate['day'])->setTime(0, 0);

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

}
