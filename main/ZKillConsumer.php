<?php


namespace Main;


use Main\Esi\Esi;
use Main\Models\Killmail;

class ZKillConsumer
{

    protected $resActions = [
        'tqStatus', 'littlekill',
    ];


    public function consume($payload): void
    {
        $data = json_decode($payload, true);
        $this->handler($data);
    }


    public function handler($data): void
    {
        if (isset($data['action'])) {
            if (in_array($data['action'], $this->resActions)) {
                $this->{$data['action']}($data);
            }
        } elseif (array_key_exists('attackers', $data)) {
            pecho('this is full km');
            $this->full($data);
        } else {
            pecho('i dont know what is this');
        }
    }

    public function full($payload): void
    {

        $array = [
            'attackers' => json_encode($payload['attackers']),
            'victim' => json_encode($payload['victim']),
            'killmail_id' => $payload['killmail_id'],
            'killmail_time' => date("Y-m-d H:i:s", strtotime($payload['killmail_time'])),
            'solar_system_id' => $payload['solar_system_id'],
            'war_id' => isset($payload['war_id']) ? $payload['war_id'] : null,
            'victim_id' => $payload['victim']['character_id'],
            'hash' => $payload['zkb']['hash']
        ];

        $killmail = new Killmail();
        $killmail->findOrCreate($array);

    }

    public function tqStatus($payload)
    {

    }


    // I know about naming

    public function littlekill($payload): void
    {

        $kmData = (new Esi)->getKillmailHash($payload['killID'], $payload['hash']);

        $array = [
            'attackers' => json_encode($kmData['attackers']),
            'victim' => json_encode($kmData['victim']),
            'killmail_id' => $kmData['killmail_id'],
            'killmail_time' => date("Y-m-d H:i:s", strtotime($kmData['kilesilmail_time'])),
            'solar_system_id' => $kmData['solar_system_id'],
            'war_id' => isset($kmData['war_id']) ? $kmData['war_id'] : null,
            'victim_id' => $kmData['victim']['character_id'],
            'hash' => $payload['hash'],
        ];

        $killmail = new Killmail();
        $killmail->findOrCreate($array);

    }


}