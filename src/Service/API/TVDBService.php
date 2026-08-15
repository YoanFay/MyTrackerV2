<?php

namespace App\Service\API;

use App\Entity\Episode;
use App\Entity\InvolvedSerieCompany;
use App\Entity\Serie;
use App\Entity\SerieCompany;
use App\Entity\SerieUpdate;
use App\Entity\TVDBGenre;
use App\Entity\TVDBTag;
use App\Entity\TVDBTagType;
use App\Repository\InvolvedSerieCompanyRepository;
use App\Repository\SerieCompanyRepository;
use App\Repository\TVDBGenreRepository;
use App\Repository\TVDBTagRepository;
use App\Repository\TVDBTagTypeRepository;
use App\Service\ImageService;
use App\Service\StringService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TVDBService
{

    private KernelInterface $kernel;

    private ObjectManager $manager;

    private SerieCompanyRepository $serieCompanyRepository;

    private InvolvedSerieCompanyRepository $involvedSerieCompanyRepository;

    private TVDBTagRepository $TVDBTagRepository;

    private TVDBTagTypeRepository $TVDBTagTypeRepository;

    private TVDBGenreRepository $TVDBGenreRepository;

    private ImageService $imageService;

    private StringService $stringService;

    private string $tvdbKey;

    /** @var array<string, SerieCompany> $companyCache */
    private array $companyCache = [];

    /** @var array<string, TVDBTag> $tagCache */
    private array $tagCache = [];

    /** @var array<string, TVDBTagType> $tagTypeCache */
    private array $tagTypeCache = [];

    /** @var array<string, TVDBGenre> $genreCache */
    private array $genreCache = [];


    public function __construct(
        ManagerRegistry                $managerRegistry,
        KernelInterface                $kernel,
        SerieCompanyRepository         $serieCompanyRepository,
        InvolvedSerieCompanyRepository $involvedSerieCompanyRepository,
        TVDBTagRepository              $TVDBTagRepository,
        TVDBTagTypeRepository          $TVDBTagTypeRepository,
        TVDBGenreRepository            $TVDBGenreRepository,
        ImageService                   $imageService,
        StringService                  $stringService,
        string                         $tvdbKey,
    )
    {

        $this->manager = $managerRegistry->getManager();
        $this->kernel = $kernel;
        $this->serieCompanyRepository = $serieCompanyRepository;
        $this->involvedSerieCompanyRepository = $involvedSerieCompanyRepository;
        $this->TVDBTagRepository = $TVDBTagRepository;
        $this->TVDBTagTypeRepository = $TVDBTagTypeRepository;
        $this->TVDBGenreRepository = $TVDBGenreRepository;
        $this->imageService = $imageService;
        $this->stringService = $stringService;
        $this->tvdbKey = $tvdbKey;
    }


    /**
     * @param int $episodeId
     *
     * @return mixed
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function getSerieIdByEpisodeId(int $episodeId): mixed
    {

        $data = self::getData("/episodes/".$episodeId);

        return $data['data']['seriesId'];

    }


    /**
     * @param string $url
     *
     * @return mixed|null
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function getData(string $url)
    {

        $client = new Client();

        $token = self::getKey();

        try {
            $response = $client->get("https://api4.thetvdb.com/v4".$url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

        } catch (Exception $e) {
            $data = null;
        }

        return $data;
    }


    /**
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getKey(): mixed
    {

        $cache = new FilesystemAdapter();

        return $cache->get('apiKeyTVDB', function (ItemInterface $item) {

            $item->expiresAfter(2592000);

            $client = new Client();

            $apiUrl = 'https://api4.thetvdb.com/v4';

            $apiToken = $this->tvdbKey;

            $response = $client->post($apiUrl."/login", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => ['apiKey' => $apiToken],
            ]);

            $data = json_decode($response->getBody(), true);

            return $data['data']['token'];
        });

    }


    /**
     * @param Serie $serie
     *
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function updateSerieInfo(Serie $serie): void
    {

        self::updateSerieName($serie);
        self::updateArtwork($serie);

    }


    /**
     * @param Serie $serie
     *
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function updateSerieName(Serie $serie): void
    {

        $data = self::getData("/series/".$serie->getTvdbId()."/translations/fra");

        if ($data !== null && $data['status'] === "success") {
            $serie->setName($data['data']['name']);
            $serie->setSlug($this->stringService->slugify($data['data']['name']));
            $serie->setIsVfName(true);
        }

        $data = self::getData("/series/".$serie->getTvdbId()."/translations/eng");

        if ($data !== null && $data['status'] === "success") {
            $serie->setNameEng($data['data']['name']);

            if (!$serie->isVfName()) {
                $serie->setName($data['data']['name']);
                $serie->setSlug($this->stringService->slugify($data['data']['name']));
            }
        }
    }


    /**
     * @param Serie $serie
     *
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function updateArtwork(Serie $serie): void
    {

        $projectDir = $this->kernel->getProjectDir();

        //$data = self::getData("/series/".$serie->getTvdbId()."/artworks?lang=fra&type=2");
        $data = self::getData("/series/".$serie->getTvdbId()."/artworks?type=2");

        $status = $data['status'];
        $data = $data['data'];

        if ($status === "success" && $data['artworks'] == []) {
            return;
        }

        $image = null;
        $score = -1;

        foreach ($data['artworks'] as $artwork) {
            if ($artwork['language'] === "fra" && $artwork['includesText'] && $artwork['score'] > $score) {
                $image = $artwork;
                $score = $artwork['score'];
            }
        }

        if ($image === null) {

            $score = -1;

            foreach ($data['artworks'] as $artwork) {
                if ($artwork['language'] === "eng" && $artwork['includesText'] && $artwork['score'] > $score) {
                    $image = $artwork;
                    $score = $artwork['score'];
                }
            }
        }

        if ($image === null && $data['image']) {

            $image = $data;

        }

        if ($image === null) {
            print_r($serie->getName()." - Pas d'artwork\n");
            return;
        }

        $this->imageService->addImage("serie/poster/", $serie->getTvdbId(), $image['image']);
    }


    /**
     * @param Episode $episode
     *
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function createEpisode(Episode $episode): void
    {

        $data = self::getData("/episodes/".$episode->getTvdbId());

        $serie = $episode->getSerie();
        if ($data !== null && $data['status'] === "success") {

            if (!$data['data']['name']) {
                $data = self::getData("/series/".$serie->getTvdbId()."/episodes/default?page=0&season=".$episode->getSeasonNumber()."&episodeNumber=".$episode->getEpisodeNumber());

                if ($data !== null && $data['status'] === "success") {
                    $episode->setTvdbId($data['data']['episodes'][0]['id']);
                    $data = self::getData("/episodes/".$episode->getTvdbId());

                }
            }

            $episode->setDuration($data['data']['runtime'] * 60000);
            if ($data['data']['number']) {
                $episode->setEpisodeNumber($data['data']['number']);
            }
            if ($data['data']['seasonNumber']) {
                $episode->setSeasonNumber($data['data']['seasonNumber']);
            }
            $episode->setName($data['data']['name'] ?? 'TBA');
            $episode->setIsNameVf(false);
            $this->updateEpisodeName($episode);
        }
    }


    /**
     * @param Episode $episode
     *
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function updateEpisodeName(Episode $episode): void
    {

        $data = self::getData("/episodes/".$episode->getTvdbId()."/translations/fra");

        if ($data !== null && $data['status'] === "success") {
            $episode->setName($data['data']['name'] ?? "TBA");
            $episode->setIsNameVf(true);
        }
    }


    /**
     * @param Serie $serie
     *
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function newSerie(Serie $serie): void
    {

        $serieData = self::getData('/series/'.$serie->getTvdbId()."/extended");

        if ($serieData['status'] === "success") {

            $serieData = $serieData['data'];

            $serie->setFirstAired($this->createDateFromString($serieData['firstAired']));
            $serie->setLastAired($this->createDateFromString($serieData['lastAired']));
            $serie->setNextAired($this->createDateFromString($serieData['nextAired']));

            $serie->setStatus($serieData['status']['name']);

            if ($serieData['tags']) {
                foreach ($serieData['tags'] as $tvdbTag) {
                    self::tvdbTagTreatment($serie, $tvdbTag);
                }
            }

            if ($serieData['genres']) {
                foreach ($serieData['genres'] as $tvdbGenre) {
                    self::tvdbGenreTreatment($serie, $tvdbGenre['name']);
                }
            }

            if ($serieData['companies']) {
                foreach ($serieData['companies'] as $company) {

                    self::companyTreatment($serie, $company);

                }
            }

        }

    }


    /**
     * @param string|null $date
     *
     * @return DateTime|null
     */
    private function createDateFromString(?string $date): ?DateTime
    {

        if (!$date) {
            return null;
        }
        $result = DateTime::createFromFormat('Y-m-d', $date);
        return $result !== false ?
            $result->setTime(0, 0) :
            null;
    }


    /**
     * @param Serie                $serie
     * @param array<string, mixed> $tag
     *
     * @return void
     */
    public function tvdbTagTreatment(Serie $serie, array $tag): void
    {

        if (isset($this->tagTypeCache[$tag['tagName']])) {
            $tvdbTagType = $this->tagTypeCache[$tag['tagName']];
        } else {
            $tvdbTagType = $this->TVDBTagTypeRepository->findOneBy(['nameEng' => $tag['tagName']]);

            if (!$tvdbTagType) {
                $tvdbTagType = new TVDBTagType();
                $tvdbTagType->setNameEng($tag['tagName']);
                $this->manager->persist($tvdbTagType);
            }

            $this->tagTypeCache[$tag['tagName']] = $tvdbTagType;
        }

        if (isset($this->tagCache[$tag['name']])) {
            $tvdbTag = $this->tagCache[$tag['name']];
        } else {
            $tvdbTag = $this->TVDBTagRepository->findOneBy(['nameEng' => $tag['name']]);

            if (!$tvdbTag) {
                $tvdbTag = new TVDBTag();
                $tvdbTag->setNameEng($tag['name']);
                $tvdbTag->setTvdbTagType($tvdbTagType);
                $this->manager->persist($tvdbTag);
            }

            $this->tagCache[$tag['name']] = $tvdbTag;
        }

        if (!$tvdbTag->hasSerie($serie)) {
            $tvdbTag->addSeries($serie);
            $this->manager->persist($tvdbTag);
        }

    }


    /**
     * @param Serie  $serie
     * @param string $genre
     *
     * @return void
     */
    public function tvdbGenreTreatment(Serie $serie, string $genre): void
    {

        if (isset($this->genreCache[$genre])) {
            $tvdbGenre = $this->genreCache[$genre];
        } else {
            $tvdbGenre = $this->TVDBGenreRepository->findOneBy(['nameEng' => $genre]);

            if (!$tvdbGenre) {
                $tvdbGenre = new TVDBGenre();
                $tvdbGenre->setNameEng($genre);
                $this->manager->persist($tvdbGenre);
            }

            $this->genreCache[$genre] = $tvdbGenre;
        }

        if (!$tvdbGenre->hasSerie($serie)) {
            $tvdbGenre->addSeries($serie);
            $this->manager->persist($tvdbGenre);
        }

    }


    /**
     * @param Serie                $serie
     * @param array<string, mixed> $company
     *
     * @return void
     */
    public function companyTreatment(Serie $serie, array $company): void
    {

        if (isset($this->companyCache[$company['name']])) {
            $serieCompany = $this->companyCache[$company['name']];
        } else {
            $serieCompany = $this->serieCompanyRepository->findOneBy(['name' => $company['name']]);

            if (!$serieCompany) {
                $serieCompany = new SerieCompany();
                $serieCompany->setName($company['name']);
                $this->manager->persist($serieCompany);
            }

            $this->companyCache[$company['name']] = $serieCompany;
        }

        $isProducer = false;
        $isNetwork = false;
        $isStudio = false;

        switch ($company['companyType']['companyTypeName']) {
        case 'Production Company':
            $research = ['isProducer' => true, 'serie' => $serie, 'company' => $serieCompany];
            $isProducer = true;
            break;
        case 'Network':
            $research = ['isNetwork' => true, 'serie' => $serie, 'company' => $serieCompany];
            $isNetwork = true;
            break;
        case 'Studio':
            $research = ['isStudio' => true, 'serie' => $serie, 'company' => $serieCompany];
            $isStudio = true;
            break;
        default:
            return;
        }

        $involved = $this->involvedSerieCompanyRepository->findOneBy($research);

        if (!$involved) {
            $involved = new InvolvedSerieCompany();
            $involved->setSerie($serie);
            $involved->setCompany($serieCompany);
            $involved->setIsProducer($isProducer);
            $involved->setIsNetwork($isNetwork);
            $involved->setIsStudio($isStudio);

            $this->manager->persist($involved);
        }

    }


    public function clearCache(): void
    {

        $this->companyCache = [];
        $this->tagCache = [];
        $this->tagTypeCache = [];
        $this->genreCache = [];
    }


    /**
     * @param Serie $serie
     *
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function updateNextAired(Serie $serie): void
    {


        $serieData = self::getData('/series/'.$serie->getTvdbId());

        $serieUpdate = new SerieUpdate();
        $serieUpdate->setUpdateDate(new DateTime());
        $serieUpdate->setSerie($serie);

        $next = null;

        if ($serieData['data']['nextAired']) {

            $next = new DateTime($serieData['data']['nextAired']);

        }

        if($serie->getNextAired() != $next){

            $serieUpdate->setAiredOld($serie->getNextAired());
            $serieUpdate->setAiredNew($next);

            $serie->setNextAired($next);

            $this->manager->persist($serie);
            $this->manager->persist($serieUpdate);

        }

        if ($serieData['data']['status']['name'] !== $serie->getStatus()) {

            $serieUpdate->setStatusOld($serie->getStatus());
            $serieUpdate->setStatusNew($serieData['data']['status']['name']);
            $serie->setStatus($serieData['data']['status']['name']);

            $this->manager->persist($serie);
            $this->manager->persist($serieUpdate);

        }

        $this->manager->flush();

    }

}
