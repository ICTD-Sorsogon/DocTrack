<?php

namespace App\Events;

use App\Models\Document;
use App\Models\Office;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $document;
    public $broadcastMe = [];
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($document)
    {
        $this->document = $document instanceof Document ? $document : Document::find($document);
        $this->user = Auth()->user();

        if($document->status == 'created'){
            array_push($this->broadcastMe, new Channel('documents37'));
        }

        if($document->status == 'received'){
            array_push($this->broadcastMe, 
                    new Channel('documents'. json_decode($document->originating_office)),
                    new Channel('documents37'));
        }

        if($document->status == 'forwarded'){
            array_push($this->broadcastMe, new Channel('documents37'));
            array_push($this->broadcastMe, new Channel('documents'. $document->originating_office));
        }

        if($document->status == 'terminated'){
            array_push($this->broadcastMe, new Channel('documents'. json_decode($document->originating_office)));
            array_push($this->broadcastMe, new Channel('documents37'));
        }

        if($document->status == 'acknowledged'){
            foreach ($document->destination_office_id as $destination){
                array_push($this->broadcastMe, new Channel('documents'. $destination)); 
            }

            array_push($this->broadcastMe, new Channel('documents'. json_decode($document->originating_office)));
        }

    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {

        // return [ new Channel('documents'.$this->document->office_id) ];
        return $this->broadcastMe;
    }
}
