<?php

use Emotality\Telegram\TelegramAPI;
use Emotality\Telegram\TelegramLogHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Monolog\LogRecord;

it('registers the telegram api singleton', function () {
    expect(app(TelegramAPI::class))
        ->toBeInstanceOf(TelegramAPI::class)
        ->toBe(app(TelegramAPI::class));
});

it('sends messages through the configured telegram api endpoint', function () {
    Http::fake([
        'https://api.telegram.org/bottest-token/sendMessage' => Http::response(['ok' => true], 200),
    ]);

    expect(app(TelegramAPI::class)->sendMessage('Test message'))->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
            && $request['chat_id'] === 'test-chat'
            && $request['parse_mode'] === 'HTML'
            && $request['text'] === 'Test message';
    });
});

it('sends formatted log records through the telegram log channel', function () {
    Http::fake([
        'https://api.telegram.org/bottest-token/sendMessage' => Http::response(['ok' => true], 200),
    ]);

    Log::channel('telegram')->error('<script>alert("boom")</script>');

    Http::assertSent(function ($request) {
        return str_contains($request['text'], '<b>Application:</b> Package Test App')
            && str_contains($request['text'], '<b>Environment:</b> Testing')
            && str_contains($request['text'], '<b>Log Level:</b> Error')
            && str_contains($request['text'], 'alert("boom")')
            && ! str_contains($request['text'], '<script>');
    });
});

it('does not send duplicate cached exceptions within the configured ttl', function () {
    Cache::flush();

    $handler = new TelegramLogHandler(300, Level::Error);
    $exception = new RuntimeException('Repeated exception');
    $record = new LogRecord(
        datetime: new DateTimeImmutable,
        channel: 'telegram',
        level: Level::Error,
        message: 'Repeated exception',
        context: ['exception' => $exception],
        extra: [],
    );

    expect($handler->isHandling($record))->toBeTrue()
        ->and($handler->isHandling($record))->toBeFalse();
});
