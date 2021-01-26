<?php

namespace App\Listeners;

use App\Events\DocumentAcknowledgeEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentAcknowledgeListener
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
    public function handle(DocumentAcknowledgeEvent $event)
    {

        $remarks = $event->remarks;
        $subject = $event->subject;

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->action = 'Document Acknowledge';
        $log->remarks = 'Document '.$subject.' has been Acknowledge. Remarks:'.$remarks;
        return $log->save();
    }
}
