# Telegram Logger for Laravel

<p>
    <a href="https://packagist.org/packages/emotality/laravel-telegram-logger"><img src="https://img.shields.io/packagist/l/emotality/laravel-telegram-logger" alt="License"></a>
    <a href="https://packagist.org/packages/emotality/laravel-telegram-logger"><img src="https://img.shields.io/packagist/v/emotality/laravel-telegram-logger" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/emotality/laravel-telegram-logger"><img src="https://img.shields.io/packagist/dt/emotality/laravel-telegram-logger" alt="Total Downloads"></a>
</p>

Laravel package to report exceptions to a Telegram chat, group or channel.

<p>
    <a href="https://www.telegram.org" target="_blank">
        <img src="https://raw.githubusercontent.com/emotality/files/master/GitHub/Telegram.png" height="50">
    </a>
</p>

## Requirements

- PHP 8.1+
- Laravel 10

## Installation

1. `composer require emotality/laravel-telegram-logger`
2. `php artisan vendor:publish --provider="Emotality\Telegram\TelegramLoggerServiceProvider"`
3. Add the following lines to your `.env`:

```dotenv
TELEGRAM_API_KEY="<telegram_api_key>"
TELEGRAM_CHAT_ID="<telegram_chat_id>"
```

4. Add the `telegram` block to the `channels` array, inside your `config/logging.php` file:

```php
'channels' => [
    ...
    'telegram' => [
        'driver' => 'telegram',
        'level' => 'error',
        'cache_ttl' => env('TELEGRAM_CACHE_TTL', 300),
    ],
],
```
###### _Note: Read more about the `cache_ttl` key below._

5. Update your log stack and add `telegram` to the `channels` array in `config/logging.php`:

```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['daily', 'telegram'],
    ...,
],
```

or change your `LOG_CHANNEL` in your `.env`:

```dotenv
LOG_CHANNEL=telegram
```

### Caching TTL explained:

A MD5 checksum is being created for every exception, then that checksum is being cached for the `cache_ttl` seconds you provide. If checksum exists in the cache, the log will not be sent.

In other words, when the exact same exception reoccurs, only the first exception will be logged, if after 300 seconds it still occurs, it will be logged again.<br>
Only the first occurrence of the same exception will be logged every 300 seconds to avoid flooding the Telegram API and your chat.

The `cache_ttl` key accepts `false` to disable caching, meaning, each and every exception will be logged to Telegram, even if it's 1000 of the same exception.

## License

laravel-telegram-logger is released under the MIT license. See [LICENSE](https://github.com/emotality/laravel-telegram-logger/blob/master/LICENSE) for details.
