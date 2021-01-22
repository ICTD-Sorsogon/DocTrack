<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfficeDeleteEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $office;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $office)
    {
        $this->user_id = $user_id;
        $this->office = $office;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
