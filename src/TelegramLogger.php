<?php

namespace Emotality\Telegram;

class TelegramLogger
{
    /**
     * Telegram API class.
     *
     * @return \Emotality\Telegram\TelegramAPI
     */
    private static function api()
    {
        return app(TelegramAPI::class);
    }

    /**
     * Send a message to a chat.
     *
     * @param  string  $message
     * @return bool
     * @throws \Emotality\Telegram\TelegramLoggerException
     */
    public static function sendMessage(string $message): bool
    {
        return self::api()->sendMessage($message);
    }
}
