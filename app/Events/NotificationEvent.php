<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;

    public function __construct($message, $userId)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        $uniqueChannel = "notification-channel_{$this->userId}";
        return new Channel($uniqueChannel);
    }

    public function broadcastAs()
    {
        return 'update-notifications';
    }

    public function broadcastWith()
    {
        return ['notification' => $this->message];
    }
}
