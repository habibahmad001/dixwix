<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    // Remove Queueable and ShouldQueue

    public function __construct(public array $data)
    {}

    public function via($notifiable)
    {
        if (app()->environment('local')) {
            return ['database'];
        }

        if (!empty($this->data['only_database']) && empty($this->data['only_email'])) {
            return ['database'];
        } elseif (!empty($this->data['only_email']) && empty($this->data['only_database'])) {
            return ['mail'];
        } else {
            return ['mail', 'database'];
        }
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->data['title'],
            'type' => $this->data['type'],
            'subject' => $this->data['subject'],
            'message' => $this->data['message'],
            'url' => $this->data['url'],
            'action' => $this->data['action'],
        ];
    }

    public function toArray($notifiable)
    {
        return $this->data;
    }
}

