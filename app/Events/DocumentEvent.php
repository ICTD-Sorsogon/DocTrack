<?php

namespace App\Events;

use App\Models\Document;
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
            if($document->wasRecentlyCreated){
                array_push($this->broadcastMe, new Channel('documents37'));
            } else {
                array_push($this->broadcastMe, new Channel('documents37'));
                array_push($this->broadcastMe, new Channel('documents'. json_decode($document->originating_office)));
            }
            
        }

        if($document->status == 'received'){
            array_push($this->broadcastMe, new Channel('documents'. json_decode($document->originating_office)));
            array_push($this->broadcastMe, new Channel('documents'. json_decode($document->sender_name)));
        }

        if($document->status == 'forwarded'){
            array_push($this->broadcastMe, new Channel('documents37'));
            array_push($this->broadcastMe, new Channel('documents'. $document->tracking_records[4]->forwarded_to));
        }

        if($document->status == 'acknowledged'){
            $document_length = count(json_decode($document->destination_office_id)); 
            for($index = 0; $index < $document_length; $index++){
                array_push($this->broadcastMe, new Channel('documents'. json_decode($document->destination_office_id)[$index]));
            }
            array_push($this->broadcastMe, new Channel('documents'. json_decode($document->sender_name)));
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
