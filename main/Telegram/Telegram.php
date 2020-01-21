<?php

namespace Main\Telegram;

use Telegram\Bot\Api;

class Telegram
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api($_ENV['TELEGRAM_BOT_TOKEN']);
    }

    public function sendSub($data, $sub): void
    {
        $response = $this->telegram->sendMessage([
            'chat_id'    => $sub->chat_id,
            'parse_mode' => 'markdown',
            'text'       => ''.PHP_EOL.'[Check this](https://zkillboard.com/kill/'.$data->killmail_id.')',
        ]);
    }
}
