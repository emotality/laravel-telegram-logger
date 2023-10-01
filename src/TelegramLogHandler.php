<?php

namespace Emotality\Telegram;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class TelegramLogHandler extends AbstractProcessingHandler
{
    /** @var int|false */
    private int|false $cache_ttl;

    /**
     * @param  int|false  $cache_ttl
     * @param  int|string|\Monolog\Level  $level
     */
    public function __construct(int|false $cache_ttl, int|string|Level $level = Level::Debug)
    {
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

            if (\Cache::has($cache_key)) {
                return false;
            }

            \Cache::put($cache_key, get_class($e), $this->cache_ttl);
        }

        return true;
    }

    /**
     * Send log to Telegram.
     *
     * @param  \Monolog\LogRecord  $record
     * @return void
     */
    protected function write(LogRecord $record): void
    {
        TelegramLogger::sendMessage(
            $this->formatMessage($record)
        );
    }

    /**
     * @param  \Monolog\LogRecord  $record
     * @return string
     */
    protected function formatMessage(LogRecord $record): string
    {
        $formatted = '<b>Application:</b> '.config('app.name').PHP_EOL;
        $formatted .= '<b>Environment:</b> '.ucfirst(config('app.env')).PHP_EOL;
        $formatted .= '<b>Log Level:</b> '.$record->level->name.PHP_EOL;
        $formatted .= '<b>Date:</b> '.$record->datetime->format('Y-m-d H:i:s').PHP_EOL;

        $message = PHP_EOL.sprintf('<pre><code class="language-text">%s</code></pre>', $record->message);

        if ((strlen($formatted) + strlen($message)) > 4000) {
            $message = substr($record->message, 0, (4000 - strlen($formatted)));
            $formatted .= PHP_EOL.sprintf('<pre><code class="language-text">%s</code></pre> ...(truncated)', trim($message));
        } else {
            $formatted .= $message;
        }

        return $formatted;
    }
}
