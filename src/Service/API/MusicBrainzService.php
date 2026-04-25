<?php

namespace App\Service\API;

use App\Entity\Music;
use App\Entity\MusicArtist;
use App\Service\ImageService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MusicBrainzService
{

    private ImageService $imageService;

    private CoverArchiveService $coverArchiveService;


    public function __construct(
        ImageService        $imageService,
        CoverArchiveService $coverArchiveService,
    )
    {

        $this->imageService = $imageService;
        $this->coverArchiveService = $coverArchiveService;
    }


    public function addMusicInfo(MusicArtist $artist, Music $music, string $albumName): void
    {

        if ($artist->getName() !== "Various Artists") {

            if (!$artist->getMbid()) {
                $dataArtist = self::searchArtist($artist->getName());

                if ($dataArtist) {

                    $artist->setName($dataArtist['name']);
                    $artist->setMbid($dataArtist['id']);
                }
            }

            $data = self::request('/release', 'arid:'.$artist->getMbid()." AND release:".$music->getName());

        } else {
            $data = self::request('/release', 'release:'.$albumName);
        }

        $music->setMusicArtist($artist);

        if ($data['releases']) {
            $data = $data['releases'];
        } else {
            dump('Release non trouvé : '.$artist->getName().' - '.$music->getName());
            return;
        }

        foreach ($data as $release) {

            if ($release['score'] !== 100) {
                continue;
            }

            if ($release['status'] != "Official" && $release['release-group']) {
                $dataRelease = self::request('/release', 'rgid:'.$release['release-group']['id']." AND status:Official and packaging:none");

                if ($dataRelease['releases'][0]) {
                    $release = $dataRelease['releases'][0];
                }
            }

            if ($release) {
                $music->setMbid($release['id']);

                $music->setDuration(self::getDuration($music->getMbid()));

                if ($dataImage = $this->coverArchiveService->request($music->getMbid())) {
                    if ($dataImage['images']) {

                        $this->imageService->addImage("musique/cover/", $music->getMbid(), $dataImage['images'][0]['image']);
                    }
                }
                break;
            }

        }
    }


    public function searchArtist(string $name)
    {

        $data = self::request('/artist', 'name:'.$name);

        foreach ($data['artists'] as $dataArtist) {

            if ($dataArtist['score'] === 100 && $dataArtist['name'] !== "(K)NoW_NAME") {
                return $dataArtist;
            }

        }

        $data = self::request('/artist', 'alias:'.$name);

        foreach ($data['artists'] as $dataArtist) {

            if ($dataArtist['score'] === 100 && $dataArtist['name'] !== "(K)NoW_NAME") {
                return $dataArtist;
            }

        }

        return false;
    }


    public function request(string $route, string $query): mixed
    {

        dump("REQUEST CALLED");

        $http = new Client([
            'timeout' => 10
        ]);

        $errorMax = 5;
        $error = 0;

        do {
            try {
                $response = $http->get('https://musicbrainz.org/ws/2'.$route.'?fmt=json&query='.$query);

                dump('https://musicbrainz.org/ws/2'.$route.'?fmt=json&query='.$query);

                return json_decode($response->getBody(), true);
            } catch (GuzzleException $e) {
                $error++;
                dump($e->getMessage());
                dump("Erreur N°".$error." sur ".$errorMax);
                sleep(5 * $error);
            }
        } while ($error < $errorMax);

        return false;

    }


    public function getDuration(string $releaseId): mixed
    {

        $data = self::request('/recording', 'reid:'.$releaseId);

        if ($data['recordings'][0] && $data['recordings'][0]['length']) {
            return $data['recordings'][0]['length'];
        }

        return null;
    }


    public function getCover(string $releaseId): mixed
    {

        $data = self::request('/recording', 'reid:'.$releaseId);

        if ($data['recordings'][0] && $data['recordings'][0]['length']) {
            return $data['recordings'][0]['length'];
        }

        return null;
    }

}
