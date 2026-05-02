<?php

namespace Emotality\Telegram\Tests;

use Emotality\Telegram\TelegramLoggerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            TelegramLoggerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.name', 'Telegram Logger Tests');
        $app['config']->set('app.env', 'testing');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('logging.channels.telegram', [
            'driver' => 'telegram',
            'level' => 'error',
            'cache_ttl' => 300,
        ]);
        $app['config']->set('telegram-logger', [
            'app_name' => 'Package Test App',
            'chat_id' => 'test-chat',
            'api_key' => 'test-token',
            'api_url' => 'https://api.telegram.org/bot',
            'api_debug' => false,
        ]);
    }
}
