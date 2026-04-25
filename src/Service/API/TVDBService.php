<?php

namespace App\Service\API;

use App\Entity\Episode;
use App\Entity\InvolvedSerieCompany;
use App\Entity\Serie;
use App\Entity\SerieCompany;
use App\Entity\TVDBGenre;
use App\Entity\TVDBTag;
use App\Entity\TVDBTagType;
use App\Repository\InvolvedSerieCompanyRepository;
use App\Repository\SerieCompanyRepository;
use App\Repository\TVDBGenreRepository;
use App\Repository\TVDBTagRepository;
use App\Repository\TVDBTagTypeRepository;
use App\Service\ImageService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
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

    private string $tvdbKey;

    private array $companyCache = [];

    private array $tagCache = [];

    private array $tagTypeCache = [];

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
        $this->tvdbKey = $tvdbKey;
    }


    public function getSerieIdByEpisodeId($episodeId)
    {

        $data = self::getData("/episodes/".$episodeId);

        return $data['data']['seriesId'];

    }


    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function getData($url)
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

        } catch (\Exception $e) {
            $data = null;
        }

        return $data;
    }


    /**
     * @throws InvalidArgumentException
     */
    public function getKey()
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


    public function updateSerieInfo(Serie $serie): void
    {

        self::updateSerieName($serie);
        self::updateArtwork($serie);

    }


    public function updateSerieName(Serie $serie): void
    {

        $data = self::getData("/series/".$serie->getTvdbId()."/translations/fra");

        if ($data !== null && $data['status'] === "success") {
            $serie->setName($data['data']['name']);
            $serie->setIsVfName(true);
        }

        $data = self::getData("/series/".$serie->getTvdbId()."/translations/eng");

        if ($data !== null && $data['status'] === "success") {
            $serie->setNameEng($data['data']['name']);
        }
    }


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
            if ($artwork['language'] === "fra" && $artwork['includesText'] && $artwork['score'] >= $score) {
                $image = $artwork;
                $score = $artwork['score'];
            }
        }

        if ($image === null) {

            $score = -1;

            foreach ($data['artworks'] as $artwork) {
                if ($artwork['language'] === "eng" && $artwork['includesText'] && $artwork['score'] >= $score) {
                    $image = $artwork;
                    $score = $artwork['score'];
                }
            }
        }

        if ($image === null) {

            $score = -1;

            foreach ($data['artworks'] as $artwork) {
                if (/* $artwork['language'] === null && */ $artwork['score'] >= $score) {
                    $image = $artwork;
                    $score = $artwork['score'];
                }
            }
        }

        if ($image === null) {
            print_r($serie->getName()." - Pas d'artwork\n");
            return;
        }

        $this->imageService->addImage("serie/poster/", $serie->getSlug(), $image['image']);
    }


    public function createEpisode(Episode $episode): void
    {

        $data = self::getData("/episodes/".$episode->getTvdbId());

        if ($data !== null && $data['status'] === "success") {

            $episode->setDuration($data['data']['runtime'] * 60000);
            $episode->setEpisodeNumber($data['data']['number']);
            $episode->setSeasonNumber($data['data']['seasonNumber']);
            $this->updateEpisodeName($episode);
        }
    }


    public function updateEpisodeName(Episode $episode): void
    {

        $data = self::getData("/episodes/".$episode->getTvdbId()."/translations/fra");

        if ($data !== null && $data['status'] === "success") {
            $episode->setName($data['data']['name']);
            $episode->setIsNameVf(true);
        }
    }


    /*public function createCompany($id): ?Company
    {

        $data = self::getData("/companies/".$id);

        if ($data !== null && $data['status'] === "success") {

            $data = $data['data'];

            $company = new Company();

            $company->setTvdbId($id);
            $company->setName($data['name']);
            $company->setType($data['companyType']['companyTypeName']);
            $company->setCountry($data['country']);

            if (isset($data['activeDate'])) {

                $startedDate = DateTime::createFromFormat('Y-m-d', $data['activeDate']);

                if ($startedDate) {
                    $company->setStartedAt($startedDate);
                }
            }

            /*if($data['parentCompany']['id']){

                $searchCompany = $this->companyRepository->findOneBy(['tvdbId' => $data['parentCompany']['id']]);

                if(!$searchCompany){
                    $company->setParent(self::createCompany($data['parentCompany']['id']));
                }else{
                    $company->setParent($searchCompany);
                }

            }//*

            $this->manager->persist($company);
            $this->manager->flush();

            return $company;

        }

        return null;

    }*/


    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function newSerie(Serie $serie): void
    {

        dump($serie);

        $serieData = self::getData('/series/'.$serie->getTvdbId()."/extended");

        dump($serieData);

        if ($serieData['status'] === "success") {

            $serieData = $serieData['data'];

            $serie->setFirstAired(\DateTime::createFromFormat('Y-m-d', $serieData['firstAired'])->setTime(0, 0) ?? null);
            $serie->setLastAired(\DateTime::createFromFormat('Y-m-d', $serieData['lastAired'])->setTime(0, 0) ?? null);
            $serie->setNextAired(\DateTime::createFromFormat('Y-m-d', $serieData['nextAired'])->setTime(0, 0) ?? null);
            $serie->setStatus($serieData['status']['name']);

            foreach ($serieData['tags'] as $tvdbTag) {
                self::tvdbTagTreatment($serie, $tvdbTag);
            }

            foreach ($serieData['genres'] as $tvdbGenre) {
                self::tvdbGenreTreatment($serie, $tvdbGenre);
            }

            foreach ($serieData['companies'] as $company) {

                self::companyTreatment($serie, $company);

            }

        }

    }


    public function tvdbTagTreatment(Serie $serie, $tag): void
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


    public function tvdbGenreTreatment(Serie $serie, $genre): void
    {

        if (isset($this->genreCache[$genre['name']])) {
            $tvdbGenre = $this->genreCache[$genre['name']];
        } else {
            $tvdbGenre = $this->TVDBGenreRepository->findOneBy(['nameEng' => $genre['name']]);

            if (!$tvdbGenre) {
                $tvdbGenre = new TVDBGenre();
                $tvdbGenre->setNameEng($genre['name']);
                $this->manager->persist($tvdbGenre);
            }

            $this->genreCache[$genre['name']] = $tvdbGenre;
        }

        if (!$tvdbGenre->hasSerie($serie)) {
            $tvdbGenre->addSeries($serie);
            $this->manager->persist($tvdbGenre);
        }

    }


    public function companyTreatment($serie, $company): void
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

        if (!$serieCompany) {
            $serieCompany = new SerieCompany();
            $serieCompany->setName($company['name']);
            $this->manager->persist($serieCompany);
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

}
