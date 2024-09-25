<?php

namespace Emotality\Telegram;

use Illuminate\Http\Client\Response;

class TelegramLoggerException extends \Exception
{
    public function __construct(Response $response)
    {
        $e = $response->toException();

        parent::__construct(
            message: $e?->getMessage() ?? 'Failed to log to Telegram.',
            code: $response->getStatusCode(),
            previous: $e
        );
    }
}
