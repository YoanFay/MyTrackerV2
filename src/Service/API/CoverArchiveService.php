<?php

namespace App\Service\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CoverArchiveService
{


    /**
     * @param string $mbid
     * @param string $type
     *
     * @return mixed
     */
    public function request(string $mbid, string $type = "release"): mixed
    {

        dump("REQUEST CALLED");

        $http = new Client([
            'timeout' => 10
        ]);

        $errorMax = 3;
        $error = 0;

        do {
            try {
                $response = $http->get("https://coverartarchive.org/".$type."/".$mbid);

                return json_decode($response->getBody(), true);
            } catch (GuzzleException $e) {
                if($e->getCode() !== 404){
                    $error++;
                    dump($e->getMessage());
                    dump("Erreur N°".$error." sur ".$errorMax);
                    sleep(5 * $error);
                }else{
                    $error = 100;
                }
            }
        } while ($error < $errorMax);

        return false;

    }

}
