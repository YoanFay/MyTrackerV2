<?php

namespace App\Service\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class IGDBService
{

    private const PUBLIC_KEY = "m16tthldb88j0ufg38rjctx9sd17g7";

    private const BASE_URL = "https://api.igdb.com/v4/";

    private string $igdbKey;


    public function __construct(
        string $igdbKey,
    )
    {

        $this->igdbKey = $igdbKey;

    }


    /**
     * @param string                    $path
     * @param array<int, string>        $fields
     * @param array<string, string|int> $wheres
     *
     * @return mixed
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public function request(string $path, array $fields, array $wheres): mixed
    {

        $bearer = $this->getKey();

        $url = self::BASE_URL.$path;

        $fieldLine = "fields ";

        $first = true;

        foreach ($fields as $field) {

            if (!$first) {
                $fieldLine .= ", ";
            }

            $fieldLine .= $field;
            $first = false;

        }

        $fieldLine .= ";";

        $whereLine = "where ";

        $first = true;

        foreach ($wheres as $key => $where) {

            if (!$first) {
                $whereLine .= " & ";
            }

            $whereLine .= $key." = ".$where;
            $first = false;

        }

        $whereLine .= ";";

        $client = new Client([
            'timeout' => 10
        ]);

        $errorMax = 5;
        $error = 0;


        do {
            try {
                $response = $client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer '.$bearer,
                        'Client-Id' => self::PUBLIC_KEY,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => $fieldLine.$whereLine,
                ]);

                return json_decode($response->getBody(), true);
            } catch (GuzzleException $e) {
                $error++;
                dump($e->getMessage());
                dump("Erreur N°".$error." sur ".$errorMax);
                if ($errorMax !== $error) {
                    sleep(10 * $error);
                }
            }
        } while ($error < $errorMax);

        return false;

    }


    /**
     * @throws InvalidArgumentException
     */
    public function getKey(): mixed
    {

        $cache = new FilesystemAdapter();

        return $cache->get('apiKeyIGDB', function (ItemInterface $item) {

            $client = new Client();

            $apiUrl = 'https://id.twitch.tv';

            $apiToken = $this->igdbKey;

            $response = $client->post($apiUrl."/oauth2/token?client_secret=".$apiToken."&grant_type=client_credentials&client_id=".self::PUBLIC_KEY);

            $data = json_decode($response->getBody(), true);

            $item->expiresAfter($data['expires_in']);

            return $data['access_token'];
        });

    }

}
