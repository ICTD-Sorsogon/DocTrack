<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentReceiveEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $subject;
    public $remarks;
    public $approved_by;
    public $through;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $subject, $remarks, $approved_by, $through)
    {
        $this->user_id = $user_id;
        $this->subject = $subject;
        $this->remarks = $remarks;
        $this->approved_by = $approved_by;
        $this->through = $through;
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
