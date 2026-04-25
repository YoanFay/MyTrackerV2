<?php

namespace App\Service;

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

        $cover = imagecreatefromstring(file_get_contents($imageLink));

        $dir = $projectDir."/public/image/".$folder;

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Chemin où enregistrer l'image
        $filename = $fileName.'.jpeg';

        // Téléchargement et enregistrement de l'image
        imagejpeg($cover, $dir.$filename, 100);
    }

}
