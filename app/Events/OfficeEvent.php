<?php

namespace App\Events;

use App\Models\Office;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfficeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $request_obj;
    public $type;
    public $old_values;
    public $offices;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $request_obj, $old_values, $type)
    {
        $this->user_id = $user_id;
        $this->request_obj = $request_obj;
        $this->old_values = $old_values;
        $this->type = $type;

        $this->offices = Office::all();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('offices');
    }
}
