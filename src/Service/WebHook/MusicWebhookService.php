<?php

namespace App\Service\WebHook;

use App\Entity\MBIDTag;
use App\Entity\MBIDTagType;
use App\Entity\Music;
use App\Entity\MusicArtist;
use App\Entity\MusicListen;
use App\Repository\MBIDTagRepository;
use App\Repository\MBIDTagTypeRepository;
use App\Repository\MusicArtistRepository;
use App\Repository\MusicRepository;
use App\Service\API\MusicBrainzService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class MusicWebhookService
{

    private MusicBrainzService $musicBrainzService;

    private MusicRepository $musicRepository;

    private MusicArtistRepository $musicArtistRepository;

    private MBIDTagTypeRepository $MBIDTagTypeRepository;

    private MBIDTagRepository $MBIDTagRepository;

    private ObjectManager $manager;


    public function __construct(
        MusicBrainzService    $musicBrainzService,
        MusicRepository       $musicRepository,
        MusicArtistRepository $musicArtistRepository,
        MBIDTagTypeRepository $MBIDTagTypeRepository,
        MBIDTagRepository     $MBIDTagRepository,
        ManagerRegistry       $managerRegistry,
    )
    {

        $this->musicBrainzService = $musicBrainzService;
        $this->musicRepository = $musicRepository;
        $this->musicArtistRepository = $musicArtistRepository;
        $this->MBIDTagTypeRepository = $MBIDTagTypeRepository;
        $this->MBIDTagRepository = $MBIDTagRepository;
        $this->manager = $managerRegistry->getManager();
    }


    public function addMusic($data, $user): void
    {

        $plexId = str_replace('plex://track/', '', $data['guid']);

        $music = $this->musicRepository->findOneBy(['plexId' => $plexId]);

        if (!$music) {

            $music = new Music();
            $music->setName($data['title']);
            $music->setPlexId($plexId);

            $artistPlexId = str_replace('plex://artist/', '', $data['grandparentGuid']);

            $artist = $this->musicArtistRepository->findOneBy(['plexId' => $artistPlexId]);

            if (!$artist) {
                $artist = new MusicArtist();
                $artist->setName($data['grandparentTitle']);
                $artist->setPlexId($artistPlexId);
            }

            $this->musicBrainzService->addMusicInfo($artist, $music, $data['parentTitle']);

            foreach ($data['Genre'] as $genre) {

                $mbidTag = $this->MBIDTagRepository->findOneBy(['plexId' => $genre['id']]);

                if (!$mbidTag) {

                    if (is_numeric($genre['tag'])) {
                        $musicTagType = "Année";
                    } else if (in_array($genre['tag'], ['Printemps', 'Été', 'Automne', 'Hiver'])) {
                        $musicTagType = "Saison";
                    } else if (in_array($genre['tag'], ['Anime', 'Film', 'Série', 'Jeux'])) {
                        $musicTagType = "Type";
                    } else if (in_array($genre['tag'], ['Opening', 'Insert', 'Ending', 'OST'])) {
                        $musicTagType = "AnimeTag";
                    } else {
                        $musicTagType = "Origin";
                    }

                    /** @var MBIDTagType $mbidTagType */
                    $mbidTagType = $this->MBIDTagTypeRepository->findOneBy(['name' => $musicTagType]);

                    $mbidTag = new MBIDTag();
                    $mbidTag->setPlexId($genre['id']);
                    $mbidTag->setMbidTagType($mbidTagType);
                    $mbidTag->setName($genre['tag']);
                    $this->manager->persist($mbidTag);

                }

                $music->addMusicTag($mbidTag);

                $this->manager->persist($artist);
                $this->manager->persist($music);

            }
        }

        $musicListen = new MusicListen();
        $musicListen->setUser($user);
        $musicListen->setMusic($music);
        $musicListen->setListenAt(new DateTime());

        $this->manager->persist($musicListen);
        $this->manager->flush();

    }


    public function importMusic($data, $user): void
    {

        $name = $data[0];
        $artistName = $data[1];
        $mbid = $data[2];
        $show = $data[13];

        $dataMusicTags = [
            [
                'musicTagPlexId' => $data[4],
                'musicTag' => $data[3],
            ],
            [
                'musicTagPlexId' => $data[6],
                'musicTag' => $data[5],
            ],
            [
                'musicTagPlexId' => $data[8],
                'musicTag' => $data[7],
            ],
            [
                'musicTagPlexId' => $data[10],
                'musicTag' => $data[9],
            ],
            [
                'musicTagPlexId' => $data[12],
                'musicTag' => $data[11],
            ],
        ];

        $music = $this->musicRepository->findByNameOrMBID($name, $mbid);

        if (!$music) {

            $music = new Music();
            $music->setName($name);

            $artist = $this->musicArtistRepository->findOneBy(['name' => $artistName]);

            if (!$artist) {
                $artist = new MusicArtist();
                $artist->setName($artistName);
            }

            $musicTagsTest = array_column($dataMusicTags, 'musicTag');

            if(in_array('Anime', $musicTagsTest)){
                $this->musicBrainzService->addMusicInfo($artist, $music, $name);
            }

            $this->manager->persist($artist);
            $this->manager->persist($music);

            foreach ($dataMusicTags as $dataMusicTag) {

                if ($dataMusicTag['musicTagPlexId']) {

                    $mbidTag = $this->MBIDTagRepository->findOneBy(['plexId' => $dataMusicTag['musicTagPlexId']]);

                    if (!$mbidTag) {

                        if (is_numeric($dataMusicTag['musicTag'])) {
                            $musicTagType = "Année";
                        } else if (in_array($dataMusicTag['musicTag'], ['Printemps', 'Été', 'Automne', 'Hiver'])) {
                            $musicTagType = "Saison";
                        } else if (in_array($dataMusicTag['musicTag'], ['Anime', 'Film', 'Série', 'Jeux'])) {
                            $musicTagType = "Type";
                        } else if (in_array($dataMusicTag['musicTag'], ['Opening', 'Insert', 'Ending', 'OST'])) {
                            $musicTagType = "AnimeTag";
                        } else {
                            $musicTagType = "Origin";
                        }

                        /** @var MBIDTagType $mbidTagType */
                        $mbidTagType = $this->MBIDTagTypeRepository->findOneBy(['name' => $musicTagType]);

                        $mbidTag = new MBIDTag();
                        $mbidTag->setPlexId($dataMusicTag['musicTagPlexId']);
                        $mbidTag->setMbidTagType($mbidTagType);
                        $mbidTag->setName($dataMusicTag['musicTag']);
                        $this->manager->persist($mbidTag);

                    }

                    $music->addMusicTag($mbidTag);

                    $this->manager->persist($artist);
                    $this->manager->persist($music);

                }

            }
        }

        dump($music);

        $musicListen = new MusicListen();
        $musicListen->setUser($user);
        $musicListen->setMusic($music);
        $musicListen->setListenAt(DateTime::createFromFormat('Y-m-d H-i-s', $show));

        $this->manager->persist($musicListen);
        $this->manager->flush();

    }

}
