<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class NewTelegramNotification extends Notification
{
    use Queueable;

    private $telegram_id;
    private $data;
    private $link;
    private $buttons;

    /**
     * Create a new notification instance.
     *
     * @param $telegram_id
     * @param $data
     * @param $link
     * @param $buttons
     */
    public function __construct($telegram_id, $data, $link = '', $buttons = [])
    {
        $this->telegram_id = $telegram_id;
        $this->data = $data;
        $this->link = $link;
        $this->buttons = $buttons;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable): TelegramMessage
    {

        $msg = TelegramMessage::create()
            ->to($this->telegram_id)
            ->video($this->link)
            ->content($this->data);
        foreach ($this->buttons as $button) {
            $msg->button($button, '/no_uri');
        }


        return $msg;
    }
}
