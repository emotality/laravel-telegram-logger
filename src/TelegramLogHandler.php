<?php

namespace Emotality\Telegram;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class TelegramLogHandler extends AbstractProcessingHandler
{
    private string $app_name;

    private string $app_env;

    private int|false $cache_ttl;

    public function __construct(int|false $cache_ttl, int|string|Level $level = Level::Debug)
    {
        $this->app_name = Config::get('telegram-logger.app_name', Config::get('app.name'));
        $this->app_env = ucfirst(Config::get('app.env'));
        $this->cache_ttl = $cache_ttl;

        parent::__construct($level);
    }

    /**
     * {@inheritDoc}
     */
    public function isHandling(LogRecord $record): bool
    {
        /** @var \Exception $e */
        if ($this->cache_ttl && ($e = $record->context['exception'] ?? false)) {
            $info = sprintf('%s:%s:%s:%d', get_class($e), $e->getCode(), $e->getFile(), $e->getLine());

            if ($e->getPrevious()) {
                $info .= sprintf('%s:%s:%s:%d', get_class($e), $e->getCode(), $e->getFile(), $e->getLine());
            }

            $cache_key = 'telegram_logger:'.md5($info);

            if (Cache::has($cache_key)) {
                return false;
            }

            Cache::put($cache_key, get_class($e), $this->cache_ttl);
        }

        return parent::isHandling($record);
    }

    /**
     * Send log to Telegram.
     */
    protected function write(LogRecord $record): void
    {
        TelegramLogger::sendMessage(
            $this->formatMessage($record)
        );
    }

    /**
     * Format the message that will be sent.
     */
    protected function formatMessage(LogRecord $record): string
    {
        $formatted = '<b>Application:</b> '.$this->app_name.PHP_EOL;
        $formatted .= '<b>Environment:</b> '.$this->app_env.PHP_EOL;
        $formatted .= '<b>Log Level:</b> '.$record->level->name.PHP_EOL;
        $formatted .= '<b>Date:</b> '.$record->datetime->format('Y-m-d H:i:s').PHP_EOL;

        $message = PHP_EOL.sprintf('<pre><code class="language-text">%s</code></pre>', strip_tags($record->message));

        if ((strlen($formatted) + strlen($message)) > 4000) {
            $message = substr($record->message, 0, (4000 - strlen($formatted)));
            $formatted .= PHP_EOL.sprintf('<pre><code class="language-text">%s</code></pre> ...(truncated)', trim($message));
        } else {
            $formatted .= $message;
        }

        return $formatted;
    }
}
