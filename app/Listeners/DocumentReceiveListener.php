<?php

namespace App\Listeners;

use App\Events\DocumentReceiveEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentReceiveListener
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
    public function handle(DocumentReceiveEvent $event)
    {
        $remarks = $event->remarks;
        $subject = $event->subject;
        $approved_by = $event->approved_by;
        $through = $event->through;

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->action = 'Document Received';
        $log->remarks = 'Document '.$subject.' has been Received through '.$through.'. Approved by: '.$approved_by.' and Remarks: '.$remarks;
        return $log->save();
    }
}
