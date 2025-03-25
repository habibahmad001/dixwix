<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $data)
    {}

    public function via($notifiable)
    {
        if (!empty($this->data['only_database']) && empty($this->data['only_mail'])) {
            return ['database'];
        } elseif (!empty($this->data['only_email']) && empty($this->data['only_database'])) {
            return ['mail'];
        } else {
            return ['mail', 'database'];
        }

    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line($this->data['message'])
            ->subject($this->data['subject'])
            ->action($this->data['action'], $this->data['url']);
    }

    public function toArray($notifiable)
    {
        return $this->data;
    }
}
