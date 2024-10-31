<?php

namespace App\Providers;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\Telegram;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramServiceProvider;

/**
 * Class TelegramServiceProvider.
 */
class CustomTelegramServiceProvider extends TelegramServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind(Telegram::class, static fn () => new Telegram(
            Cache::get('token_bot_telegram', null),
            app(HttpClient::class),
            config('services.telegram-bot-api.base_uri')
        ));

        Notification::resolved(static function (ChannelManager $service) {
            $service->extend('telegram', static fn ($app) => $app->make(TelegramChannel::class));
        });
    }
}
