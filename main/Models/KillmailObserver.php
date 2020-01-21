<?php

namespace Main\Models;

use Main\Telegram\Telegram;
use SplObserver;
use SplSubject;

class KillmailObserver implements SplObserver
{
    public function update(SplSubject $subject)
    {
        $subArray = $this->subscriberBuilder($subject);
        $subs = (new KillmailSubscriber())->find($subArray);

        foreach ($subs as $sub) {
            (new Telegram())->sendSub($subject, $sub);
        }
    }

    private function subscriberBuilder($data)
    {
        $array = [];

        $attackers = json_decode($data->attackers, true);

        foreach ($attackers as $attacker) {
            if (isset($attacker['alliance_id'])) {
                $array[] = "attackers:alliance_id:{$attacker['alliance_id']}";
            }
            if (isset($attacker['corporation_id'])) {
                $array[] = "attackers:corporation_id:{$attacker['corporation_id']}";
            }
            if (isset($attacker['character_id'])) {
                $array[] = "attackers:character_id:{$attacker['character_id']}";
            }
            if (isset($attacker['faction_id'])) {
                $array[] = "attackers:faction_id:{$attacker['faction_id']}";
            }
            $array[] = "attackers:ship_type_id:{$attacker['ship_type_id']}";
        }

        $victim = json_decode($data->victim, true);

        if (isset($victim['alliance_id'])) {
            $array[] = "victim:alliance_id:{$victim['alliance_id']}";
        }
        $array[] = "victim:corporation_id:{$victim['corporation_id']}";
        $array[] = "victim:character_id:{$victim['character_id']}";
        $array[] = "victim:ship_type_id:{$victim['ship_type_id']}";

        $array[] = "space:solar_system_id:{$data->solar_system_id}";
        if (isset($data->war_id)) {
            $array[] = "war_id:{$data->war_id}";
        }

        return $array;
    }
}
