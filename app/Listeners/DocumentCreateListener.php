<?php

namespace App\Listeners;

use App\Events\DocumentCreateEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentCreateListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DocumentCreateEvent $event)
    {
        $subject = $event->request_obj->subject;
        $data = json_encode($event->request_obj);

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->new_values = $data;
        $log->action = 'Document Created';
        $log->remarks = 'New Document has been Created with Subject of : '.$subject;
        return $log->save();
    }
}
