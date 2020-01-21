<?php


namespace Main\Esi;


use GuzzleHttp\Client;

class Esi
{
    protected $client;
    protected $uri = 'https://esi.evetech.net/latest/';
    protected $source = '/?datasource=tranquility';

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getCharacter(int $id): array
    {
        $res = $this->client->request('GET', $this->uri . "characters/$id" . $this->source);

        return json_decode($res->getBody(), true);
    }

    public function getKillmailHash(int $killmailId, string $hash): array
    {
        $res = $this->client->request('GET', $this->uri . "killmails/$killmailId/$hash" . $this->source);

        return json_decode($res->getBody(), true);
    }


}