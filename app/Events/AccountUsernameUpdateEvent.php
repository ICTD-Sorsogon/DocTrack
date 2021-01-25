<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountUsernameUpdateEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $request_obj;
    public $old_values;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $request_obj, $old_values)
    {
        $this->user_id = $user_id;
        $this->request_obj = $request_obj;
        $this->old_values = $old_values;
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
