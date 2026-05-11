<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\WebHook\MovieWebhookService;
use App\Service\WebHook\MusicWebhookService;
use App\Service\WebHook\SerieWebhookService;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:import',
    description: 'Add a short description for your command',
)]
class ImportCommand extends Command
{

    private KernelInterface $kernel;

    private MovieWebhookService $movieWebhookService;

    private MusicWebhookService $musicWebhookService;

    private SerieWebhookService $serieWebhookService;

    private ManagerRegistry $doctrine;


    public function __construct(
        KernelInterface     $kernel,
        MovieWebhookService $movieWebhookService,
        MusicWebhookService $musicWebhookService,
        SerieWebhookService $serieWebhookService,
        ManagerRegistry     $doctrine,
    )
    {

        $this->kernel = $kernel;
        $this->movieWebhookService = $movieWebhookService;
        $this->musicWebhookService = $musicWebhookService;
        $this->serieWebhookService = $serieWebhookService;
        $this->doctrine = $doctrine;
        parent::__construct();
    }


    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $errorDir = $this->kernel->getProjectDir().'/public/error/';

        if (!is_dir($errorDir)) {
            mkdir($errorDir, 0777, true);
        }

        // MOVIE
        $filePath = $this->kernel->getProjectDir().'/public/import/movie.csv';
        $movieErrorDir = $errorDir.'/movie/';

        if (!is_dir($movieErrorDir)) {
            mkdir($movieErrorDir, 0777, true);
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $header = array_shift($lines);

        foreach ($lines as $line) {
            $data = str_getcsv($line, ";");
            $io->note($data);

            try {
                $this->resetEntityManagerIfClosed();
                $user = $this->getManagedUser();
                $this->movieWebhookService->importMovie($data, $user);
                $this->doctrine->getManager()->clear();
            } catch (Exception $e) {
                $this->logError($movieErrorDir, $data, $e);
                $this->doctrine->resetManager();
            }

            array_shift($lines);
            file_put_contents($filePath, $header.PHP_EOL.implode(PHP_EOL, $lines));
        }

        // MUSIC
        $filePath = $this->kernel->getProjectDir().'/public/import/music.csv';
        $musicErrorDir = $errorDir.'/music/';

        if (!is_dir($musicErrorDir)) {
            mkdir($musicErrorDir, 0777, true);
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $header = array_shift($lines);

        foreach ($lines as $line) {
            $data = str_getcsv($line, ";");
            $io->note($data);

            try {
                $this->resetEntityManagerIfClosed();
                $user = $this->getManagedUser();
                $this->musicWebhookService->importMusic($data, $user);
                $this->doctrine->getManager()->clear();
            } catch (Exception $e) {
                $this->logError($musicErrorDir, $data, $e);
                $this->doctrine->resetManager();
            }

            array_shift($lines);
            file_put_contents($filePath, $header.PHP_EOL.implode(PHP_EOL, $lines));
        }

        // SERIE
        $filePath = $this->kernel->getProjectDir().'/public/import/episode.csv';
        $episodeErrorDir = $errorDir.'/episode/';

        if (!is_dir($episodeErrorDir)) {
            mkdir($episodeErrorDir, 0777, true);
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $header = array_shift($lines);

        foreach ($lines as $line) {
            $data = str_getcsv($line, ";");
            $io->note($data);

            try {
                $this->resetEntityManagerIfClosed();
                $user = $this->getManagedUser();
                $this->serieWebhookService->importSerie($data, $user);
                $this->doctrine->getManager()->clear();
                $this->serieWebhookService->clearCaches();
            } catch (Exception $e) {
                $this->logError($episodeErrorDir, $data, $e);
                $this->doctrine->resetManager();
                $this->serieWebhookService->clearCaches();
            }

            array_shift($lines);
            file_put_contents($filePath, $header.PHP_EOL.implode(PHP_EOL, $lines));
        }

        return Command::SUCCESS;
    }


    private function resetEntityManagerIfClosed(): void
    {
        $em = $this->doctrine->getManager();
        assert($em instanceof EntityManagerInterface);

        if (!$em->isOpen()) {
            $this->doctrine->resetManager();
        }
    }


    /**
     * @param string             $dir
     * @param array<int, string> $data
     * @param Exception          $e
     *
     * @return void
     * @throws Exception
     */
    private function logError(string $dir, array $data, Exception $e): void
    {

        $date = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $fileName = $dir.$date->format('Y-m-d_H-i-s').'.log';

        $logContent = sprintf(
            "[%s] Erreur d'import\nDonnées : %s\nErreur : %s\nTrace : %s",
            $date->format('Y-m-d H:i:s'),
            implode(';', $data),
            $e->getMessage(),
            $e->getTraceAsString()
        );

        file_put_contents($fileName, $logContent);
    }


    private function getManagedUser(): ?User
    {

        return $this->doctrine->getManager()
            ->getRepository(User::class)
            ->findOneBy(['plexName' => "yoan.f8"]);
    }
}
