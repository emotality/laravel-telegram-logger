<?php

namespace Emotality\Telegram;

use Illuminate\Http\Client\PendingRequest;

class TelegramAPI
{
    /** @var \Illuminate\Http\Client\PendingRequest */
    protected PendingRequest $client;

    /** @var string */
    private string $base_url;

    /**
     * @var array
     */
    public array $config = [
        'chat_id'   => 'your_chat_id',
        'api_key'   => 'your_api_key',
        'api_url'   => 'https://api.telegram.org/bot',
        'api_debug' => false,
    ];

    /**
     * TelegramAPI constructor.
     *
     * @return void
     * @throws \Emotality\Telegram\TelegramLoggerException
     */
    public function __construct()
    {
        $this->config = config('telegram-logger') ?? $this->config;

        $this->base_url = $this->config['api_url'].$this->config['api_key'];

        $this->client = \Http::baseUrl($this->base_url)->withOptions([
            'debug'           => $this->config['api_debug'],
            //'verify'          => true,
            //'version'         => 2.0,
            'connect_timeout' => 15,
            'timeout'         => 30,
        ])->acceptJson();
    }

    /**
     * Handle API request to send message.
     *
     * @param  string  $message
     * @return bool
     * @see https://core.telegram.org/bots/api#sendmessage
     */
    public function sendMessage(string $message): bool
    {
        $data = [
            'chat_id'    => $this->config['chat_id'],
            'parse_mode' => 'HTML',
            'text'       => $message,
        ];

        if ($this->config['api_debug'] ?? false) {
            \Log::channel('single')->debug(
                sprintf("TELEGRAM-LOGGER API REQUEST:\n%s/sendMessage\n%s", $this->base_url, json_encode($data, 128))
            );
        }

        $response = $this->client->post('/sendMessage', $data);

        if ($this->config['api_debug'] ?? false) {
            \Log::channel('single')->debug(
                sprintf("TELEGRAM-LOGGER API RESPONSE: [%d]\n%s", $response->status(), json_encode($response->json(), 128))
            );
        }

        return $response->successful();
    }
}
