<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameCollection;
use App\Entity\GameCompany;
use App\Entity\GameMode;
use App\Entity\GamePlatform;
use App\Entity\GameRelease;
use App\Entity\GameReleaseStatus;
use App\Entity\GameTracker;
use App\Entity\IGDBGenre;
use App\Entity\IGDBTheme;
use App\Entity\InvolvedGameCompany;
use App\Entity\PlayerPerspective;
use App\Entity\User;
use App\Form\GameType;
use App\Repository\GameCompanyRepository;
use App\Repository\GamePlatformRepository;
use App\Repository\GameReleaseStatusRepository;
use App\Repository\GameRepository;
use App\Repository\GameTrackerRepository;
use App\Repository\UserRepository;
use App\Service\API\IGDBService;
use App\Service\ImageService;
use App\Service\StringService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

final class GameController extends AbstractController
{
    #[Route('/game', name: 'game')]
    public function index(
        GameRepository $gameRepository,
    ): Response
    {

        $games = $gameRepository->findAll();

        return $this->render('game/index.html.twig', [
            'games' => $games,
        ]);
    }


    #[Route('/game/{id}', name: 'game_details', requirements: ['id' => '\d+'])]
    public function details(
        KernelInterface $kernel,
        GameRepository  $gameRepository,
        int             $id,
    ): Response
    {

        $game = $gameRepository->find($id);

        $companies = [
            'developer' => [
                'frenchName' => 'Développeur',
                'list' => [],
            ],
            'porting' => [
                'frenchName' => 'Portage',
                'list' => [],
            ],
            'publisher' => [
                'frenchName' => 'Éditeur',
                'list' => [],
            ],
            'supporting' => [
                'frenchName' => 'Développeur support',
                'list' => [],
            ],
        ];

        foreach ($game->getInvolvedGameCompanies()->getValues() as $company) {

            if ($company->isDeveloper()) {
                $companies['developer']['list'][] = $company->getGameCompany();
            }

            if ($company->isPorting()) {
                $companies['porting']['list'][] = $company->getGameCompany();
            }

            if ($company->isPublisher()) {
                $companies['publisher']['list'][] = $company->getGameCompany();
            }

            if ($company->isSupporting()) {
                $companies['supporting']['list'][] = $company->getGameCompany();
            }

        }

        $background = false;

        if (file_exists($kernel->getProjectDir().'/public/image/game/background/'.$game->getIGDBId().'.jpeg')) {
            $background = true;
        }

        return $this->render('game/details.html.twig', [
            'game' => $game,
            'companies' => $companies,
            'background' => $background,
        ]);
    }


    #[Route('/game/{id}/new', name: 'game_new', requirements: ['id' => '\d+'])]
    public function newGame(
        KernelInterface $kernel,
        Request         $request,
        ManagerRegistry $managerRegistry,
        GameRepository  $gameRepository,
        UserRepository  $userRepository,
        int             $id,
    ): Response
    {

        /** @var Game $game */
        $game = $gameRepository->findOneBy(['id' => $id]);

        $platform = [];

        foreach ($game->getGameReleases() as $gameRelease) {

            $platform[$gameRelease->getGamePlatform()->getName()] = $gameRelease->getGamePlatform();

        }

        $form = $this->createFormBuilder()
            ->add('platform', ChoiceType::class, [
                'label' => 'Plateforme',
                'choices' => $platform,
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary mb-1'
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $managerRegistry->getManager();

            $result = $form->getData();

            /** @var User $user */
            $user = $userRepository->findOneBy(['plexName' => 'yoan.f8']);

            $tracker = new GameTracker();
            $tracker->setGame($game);
            $tracker->setGamePlatform($result['platform']);
            $tracker->setUser($user);
            $tracker->setStartDate($result['startDate']);

            $em->persist($tracker);
            $em->flush();
            $em->flush();

            return $this->redirectToRoute('game_details', ['id' => $game->getId()]);

        }

        $background = false;

        if (file_exists($kernel->getProjectDir().'/public/image/game/background/'.$game->getIGDBId().'.jpeg')) {
            $background = true;
        }


        return $this->render('game/tracker.html.twig', [
            'title' => 'Nouvelle partie',
            'formTitle' => 'Commencer une nouvelle partie de : '.$game->getName(),
            'form' => $form,
            'game' => $game,
            'background' => $background,
        ]);

    }


    /**
     * @param Request                     $request
     * @param ManagerRegistry             $managerRegistry
     * @param GameRepository              $gameRepository
     * @param GamePlatformRepository      $gamePlatformRepository
     * @param GameReleaseStatusRepository $gameReleaseStatusRepository
     * @param GameCompanyRepository       $companyRepository
     * @param IGDBService                 $IGDBService
     * @param StringService               $stringService
     * @param ImageService                $imageService
     *
     * @return Response
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    #[Route('/game/add', name: 'game_add')]
    public function add(
        Request                     $request,
        ManagerRegistry             $managerRegistry,
        GameRepository              $gameRepository,
        GamePlatformRepository      $gamePlatformRepository,
        GameReleaseStatusRepository $gameReleaseStatusRepository,
        GameCompanyRepository       $companyRepository,
        IGDBService                 $IGDBService,
        StringService               $stringService,
        ImageService                $imageService,
    ): Response
    {

        $form = $this->createForm(GameType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $form->getData()['igdbId'];

            if (!$id) {

                return $this->render('game/index.html.twig', [
                    'controller_name' => 'GameController',
                ]);

            }

            $game = $gameRepository->findOneBy(['igdbId' => $id]);

            if (!$game) {

                $em = $managerRegistry->getManager();

                $gameData = $IGDBService->request('games', ['name', 'aggregated_rating', 'aggregated_rating_count', 'rating', 'rating_count', 'cover.url', 'collections.name', 'game_modes.name', 'involved_companies.company.name', 'involved_companies.company.start_date', 'involved_companies.developer', 'involved_companies.porting', 'involved_companies.publisher', 'involved_companies.supporting', 'player_perspectives.name', 'genres.name', 'themes.name'], ['id' => $id])[0];

                $game = new Game();

                $game->setName($gameData['name']);
                $game->setSlug($stringService->slugify($gameData['name']));
                $game->setIgdbId($id);
                $game->setRating($gameData['rating'] ?? null);
                $game->setRatingCount($gameData['rating_count'] ?? null);
                $game->setAggregatedRating($gameData['aggregated_rating'] ?? null);
                $game->setAggregatedRatingCount($gameData['aggregated_rating_count'] ?? null);

                $frenchData = $IGDBService->request('alternative_names', ['name'], ['game' => $id, 'comment' => '"French title"']);

                if ($frenchData) {

                    $game->setName($frenchData[0]['name']);
                    $game->setSlug($stringService->slugify($frenchData[0]['name']));

                }

                $releaseDatas = $IGDBService->request('release_dates', ['date', 'platform.name', 'status.name'], ['game' => $game->getIgdbId(), 'release_region' => '(1,8)']);

                foreach ($releaseDatas as $releaseData) {

                    if ($releaseData['platform']['name'] === "Google Stadia") {
                        continue;
                    }

                    $release = new GameRelease();

                    $date = new DateTime();
                    $date->setTimestamp($releaseData['date']);

                    $release->setReleaseDate($date);

                    $platform = $gamePlatformRepository->findOneBy(['name' => $releaseData['platform']['name']]);

                    if (!$platform) {
                        $platform = new GamePlatform();

                        $platform->setName($releaseData['platform']['name']);
                        $platform->setSlug($stringService->slugify($releaseData['platform']['name']));

                        $em->persist($platform);
                    }

                    $release->setGamePlatform($platform);

                    $statusName = 'Full Release';

                    if (isset($releaseData['status'])) {

                        $statusName = $releaseData['status']['name'];

                    }

                    $status = $gameReleaseStatusRepository->findOneBy(['name' => $statusName]);

                    if (!$status) {

                        $status = new GameReleaseStatus();
                        $status->setName($statusName);

                        $em->persist($status);

                    }

                    $release->setStatus($status);

                    $em->persist($release);

                    $game->addGameRelease($release);

                }

                if (isset($gameData['collections'])) {

                    foreach ($gameData['collections'] as $collectionData) {

                        $collection = new GameCollection();
                        $collection->setName($collectionData['name']);
                        $collection->setSlug($stringService->slugify($collectionData['name']));

                        $em->persist($collection);

                        $game->addGameCollection($collection);

                    }

                }

                if (isset($gameData['involved_companies'])) {

                    foreach ($gameData['involved_companies'] as $companyData) {

                        $company = $companyRepository->findOneBy(['igdbId' => $companyData['company']['id']]);

                        if (!$company) {

                            $company = new GameCompany();
                            $company->setName($companyData['company']['name']);
                            $company->setSlug($stringService->slugify($companyData['company']['name']));
                            $company->setIgdbId($companyData['company']['id']);

                            if (isset($companyData['company']['start_date'])) {

                                $startDate = new DateTime();
                                $startDate->setTimestamp($companyData['company']['start_date']);

                                $company->setCreatedAt($startDate);
                            }

                            $em->persist($company);
                        }

                        $involved = new InvolvedGameCompany();
                        $involved->setGameCompany($company);
                        $involved->setIsDeveloper($companyData['developer']);
                        $involved->setIsPorting($companyData['porting']);
                        $involved->setIsPublisher($companyData['publisher']);
                        $involved->setIsSupporting($companyData['supporting']);

                        $em->persist($involved);

                        $game->addInvolvedGameCompany($involved);
                    }

                }

                if (isset($gameData['game_modes'])) {

                    foreach ($gameData['game_modes'] as $gameModeData) {

                        $gameMode = new GameMode();
                        $gameMode->setNameEng($gameModeData['name']);

                        $em->persist($gameMode);

                        $game->addGameMode($gameMode);
                    }

                }

                if (isset($gameData['player_perspectives'])) {

                    foreach ($gameData['player_perspectives'] as $playerPerspectiveData) {

                        $playerPerspective = new PlayerPerspective();
                        $playerPerspective->setNameEng($playerPerspectiveData['name']);

                        $em->persist($playerPerspective);

                        $game->addPlayerPerspective($playerPerspective);
                    }

                }

                if (isset($gameData['genres'])) {

                    foreach ($gameData['genres'] as $genreData) {

                        $genre = new IGDBGenre();
                        $genre->setNameEng($genreData['name']);

                        $em->persist($genre);

                        $game->addIgdbGenre($genre);
                    }

                }

                if (isset($gameData['themes'])) {

                    foreach ($gameData['themes'] as $ThemeData) {

                        $theme = new IGDBTheme();
                        $theme->setNameEng($ThemeData['name']);

                        $em->persist($theme);

                        $game->addIgdbTheme($theme);
                    }

                }

                $em->persist($game);
                $em->flush();

                $imageService->addImage("game/cover/", $game->getIgdbId(), str_replace('/t_thumb/', '/t_1080p/', $gameData['cover']['url']));

                try {

                    $background = $IGDBService->request('artworks', ['url'], ['game' => $game->getIGDBId(), 'artwork_type' => '1', 'alpha_channel' => 'false'])[0];

                    $imageService->addImage("game/background/", $game->getIgdbId(), str_replace('/t_thumb/', '/t_1080p/', $background['url']));

                } catch (Exception $e) {
                }


                return $this->redirectToRoute('game_details', [
                    'id' => $game->getId(),
                ]);

            }

        }

        return $this->render('game/add.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/game/{id}/end', name: 'game_end', requirements: ['id' => '\d+'])]
    public function endGame(
        KernelInterface       $kernel,
        Request               $request,
        ManagerRegistry       $managerRegistry,
        GameTrackerRepository $gameTrackerRepository,
        int                   $id,
    ): Response
    {

        /** @var GameTracker $gameTracker */
        $gameTracker = $gameTrackerRepository->findOneBy(['id' => $id]);

        $game = $gameTracker->getGame();

        $form = $this->createFormBuilder()
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('endTime', TextType::class, [
                'label' => 'Temps pour finir',
                'attr' => [
                    'placeholder' => '--h--',
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('isNoComplete', CheckboxType::class, [
                'label' => 'Ce jeu n\'as pas de 100%',
                'required' => false,
                'label_attr' => [
                    'class' => 'me-2'
                ],
                'attr' => [
                    'class' => 'mb-3 form-check-input'
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary mb-1'
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $managerRegistry->getManager();

            $result = $form->getData();

            $gameTracker->setEndDate($result['endDate']);

            $endTime = explode('h', $result['endTime']);

            $endMin = intval($endTime[1]) + intval($endTime[0]) * 60;

            $gameTracker->setEndTime($endMin);

            $gameTracker->setIsNoComplete($result['isNoComplete']);

            $em->persist($gameTracker);
            $em->flush();
            $em->flush();

            return $this->redirectToRoute('game_details', ['id' => $game->getId()]);

        }

        $background = false;

        if (file_exists($kernel->getProjectDir().'/public/image/game/background/'.$game->getIGDBId().'.jpeg')) {
            $background = true;
        }

        return $this->render('game/tracker.html.twig', [
            'title' => 'Terminer',
            'formTitle' => 'Terminer : '.$game->getName(),
            'form' => $form,
            'game' => $game,
            'background' => $background,
        ]);

    }


    #[Route('/game/{id}/complete', name: 'game_complete', requirements: ['id' => '\d+'])]
    public function completeGame(
        KernelInterface       $kernel,
        Request               $request,
        ManagerRegistry       $managerRegistry,
        GameTrackerRepository $gameTrackerRepository,
        int                   $id,
    ): Response
    {

        /** @var GameTracker $gameTracker */
        $gameTracker = $gameTrackerRepository->findOneBy(['id' => $id]);

        $game = $gameTracker->getGame();

        $form = $this->createFormBuilder()
            ->add('completeDate', DateType::class, [
                'label' => 'Date de 100%',
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('completeTime', TextType::class, [
                'label' => 'Temps pour 100%',
                'attr' => [
                    'placeholder' => '--h--',
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary mb-1'
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $managerRegistry->getManager();

            $result = $form->getData();

            $gameTracker->setCompleteDate($result['completeDate']);

            $completeTime = explode('h', $result['completeTime']);

            $completeMin = intval($completeTime[1]) + intval($completeTime[0]) * 60;

            $gameTracker->setCompleteTime($completeMin);

            $em->persist($gameTracker);
            $em->flush();
            $em->flush();

            return $this->redirectToRoute('game_details', ['id' => $game->getId()]);

        }

        $background = false;

        if (file_exists($kernel->getProjectDir().'/public/image/game/background/'.$game->getIGDBId().'.jpeg')) {
            $background = true;
        }

        return $this->render('game/tracker.html.twig', [
            'title' => '100%',
            'formTitle' => 'Terminer à 100% : '.$game->getName(),
            'form' => $form,
            'game' => $game,
            'background' => $background,
        ]);

    }
}
