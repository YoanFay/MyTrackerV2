<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageService
{

    private KernelInterface $kernel;


    public function __construct(
        KernelInterface $kernel,
    )
    {
        $this->kernel = $kernel;
    }


    public function addImage($folder, $fileName, $imageLink)
    {

        $projectDir = $this->kernel->getProjectDir();


        $client = new Client([
            'timeout'         => 30,
            'allow_redirects' => true,
            'headers'         => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ],
        ]);

        try {
            $imageContent = $client->get($imageLink)->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new RuntimeException("Impossible de télécharger l'image : " . $e->getMessage());
        }

        $cover = imagecreatefromstring($imageContent);

        if ($cover === false) {
            throw new \RuntimeException("Impossible de créer l'image depuis l'URL : $imageContent");
        }

        $dir = $projectDir."/public/image/".$folder;

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Téléchargement et enregistrement de l'image
        imagejpeg($cover, $dir.$fileName.'.jpeg', 100);
        imagedestroy($cover); // Libérer la mémoire
    }

}
