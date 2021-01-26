<?php

namespace App\Listeners;

use App\Events\DocumentUpdateEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentUpdateListener
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
    public function handle(DocumentUpdateEvent $event)
    {

        $subject = $event->request_obj->subject;
        $old_values = json_encode($event->old_values);
        $old_subject = $event->old_values->subject;
        $data = json_encode($event->request_obj);

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->new_values = $data;
        $log->original_values = $old_values;
        $log->action = 'Document Updated';
        $log->remarks = 'Document has been Update from : '.$old_subject.' to '.$subject;
        return $log->save();
    }
}
