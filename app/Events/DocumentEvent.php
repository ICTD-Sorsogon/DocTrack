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

        // if($document->destination_office_id){
            $document_length = count(json_decode($document->destination_office_id));
    
            for($index = 0; $index < $document_length; $index++){
                array_push($this->broadcastMe, new Channel('documents'. json_decode($document->destination_office_id)[$index]));
            }
        // }

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
