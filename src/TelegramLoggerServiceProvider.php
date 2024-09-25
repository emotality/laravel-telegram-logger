<?php

namespace Emotality\Telegram;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class TelegramLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TelegramAPI::class, fn () => new TelegramAPI);

        $this->app['log']->extend('telegram', function ($app, array $config) {
            return new \Monolog\Logger('telegram-logger', [
                new TelegramLogHandler($app['config']['app'], $config['cache_ttl'], $config['level']),
            ]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/telegram-logger.php' => App::configPath('telegram-logger.php'),
            ], 'config');
        }
    }
}
