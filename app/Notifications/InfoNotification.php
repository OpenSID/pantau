<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class InfoNotification extends Notification
{
    use Queueable;

    private string $message;

    private $userTelegram;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userTelegram, string $message)
    {
        $this->userTelegram = $userTelegram;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->to($this->userTelegram) // Optional.
            ->content($this->message); // Markdown supported.
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
