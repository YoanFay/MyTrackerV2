<?php

namespace App\Service\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CoverArchiveService
{


    public function request($mbid): mixed
    {

        dump("REQUEST CALLED");

        $http = new Client([
            'timeout' => 10
        ]);

        $errorMax = 5;
        $error = 0;

        do {
            try {
                $response = $http->get("https://coverartarchive.org/release/".$mbid);

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

}
