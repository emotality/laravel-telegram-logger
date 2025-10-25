<?php

namespace Emotality\Telegram;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramAPI
{
    private PendingRequest $client;

    private string $base_url;

    private array $config = [
        'app_name'  => null,
        'chat_id'   => null,
        'api_key'   => null,
        'api_url'   => 'https://api.telegram.org/bot',
        'api_debug' => false,
    ];

    /**
     * TelegramAPI constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = Config::get('telegram-logger', $this->config);

        $this->base_url = $this->config['api_url'].$this->config['api_key'];

        $this->client = Http::baseUrl($this->base_url)->withOptions([
            'debug'           => $this->config['api_debug'],
            'verify'          => true,
            'version'         => 2.0,
            'connect_timeout' => 15,
            'timeout'         => 30,
        ])->acceptJson();
    }

    /**
     * Handle API request to send message.
     *
     * @see https://core.telegram.org/bots/api#sendmessage
     * @throws \Emotality\Telegram\TelegramLoggerException
     */
    public function sendMessage(string $message): bool
    {
        $data = [
            'chat_id'    => $this->config['chat_id'],
            'parse_mode' => 'HTML',
            'text'       => $message,
        ];

        if ($this->config['api_debug'] ?? false) {
            Log::channel('single')->debug(
                sprintf("TELEGRAM-LOGGER API REQUEST:\n%s/sendMessage\n%s", $this->base_url, json_encode($data, 128))
            );
        }

        $response = $this->client->post('/sendMessage', $data)->onError(
            fn (Response $response) => App::get(ExceptionHandler::class)->report(new TelegramLoggerException($response))
        );

        if ($this->config['api_debug'] ?? false) {
            Log::channel('single')->debug(
                sprintf("TELEGRAM-LOGGER API RESPONSE: [%d]\n%s", $response->status(), json_encode($response->json(), 128))
            );
        }

        return $response->successful();
    }
}
