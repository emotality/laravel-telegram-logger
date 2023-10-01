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

- PHP 8.0+
- Laravel 9.0+

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
        'delay_send' => env('TELEGRAM_DELAY', 300),
    ],
],
```
###### _Note: `delay_send` is only used for recurring exceptions. Recurring exceptions will only be logged once every 300 seconds to avoid flooding the API and chat._

5. Update your log stack and add `telegram` to the `channels` array in `config/logging.php`:

```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['daily', 'telegram'],
    'ignore_exceptions' => false,
],
```

or change your `LOG_CHANNEL` in your `.env`:

```dotenv
LOG_CHANNEL=telegram
```

## License

laravel-telegram-logger is released under the MIT license. See [LICENSE](https://github.com/emotality/laravel-telegram-logger/blob/master/LICENSE) for details.
