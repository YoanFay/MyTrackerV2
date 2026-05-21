<?php

namespace App\Command;

use App\Repository\SerieRepository;
use App\Service\API\AniListService;
use App\Service\API\TVDBService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-date',
    description: 'Add a short description for your command',
)]
class UpdateDateCommand extends Command
{

    private SerieRepository $serieRepository;

    private AniListService $aniListService;

    private TVDBService $TVDBService;


    public function __construct(
        SerieRepository $serieRepository,
        AniListService  $aniListService,
        TVDBService     $TVDBService,
    )
    {

        $this->serieRepository = $serieRepository;
        $this->aniListService = $aniListService;
        $this->TVDBService = $TVDBService;
        parent::__construct();
    }


    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $series = $this->serieRepository->findBy(['isFollow' => true]);

        foreach ($series as $serie) {

            if ($serie->getSerieType()->getName() === "Anime") {

                $this->aniListService->updateNextDate($serie);

            } else {

                $this->TVDBService->updateNextAired($serie);

            }

        }
        return Command::SUCCESS;
    }
}
