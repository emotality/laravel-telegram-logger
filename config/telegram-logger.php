<?php

return [

    /*
     * Chat ID that will be receiving the messages.
     * See: https://stackoverflow.com/a/32572159
     */
    'chat_id' => env('TELEGRAM_CHAT_ID'),

    /*
     * API key that looks something like this: 9427684431:AAGJy43qRZ-cOWu3OxNYrvz3clOl21wIJHI
     */
    'api_key' => env('TELEGRAM_API_KEY'),

    /*
     * If not self-hosting, leave as is.
     */
    'api_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org/bot'),

    /*
     * API request and response will be logged using "single" log channel.
     */
    'api_debug' => env('TELEGRAM_API_DEBUG', false),

];
