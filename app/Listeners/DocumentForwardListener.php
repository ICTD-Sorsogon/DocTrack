<?php

namespace App\Listeners;

use App\Events\DocumentForwardEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentForwardListener
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
    public function handle(DocumentForwardEvent $event)
    {
        $remarks = $event->remarks;
        $subject = $event->subject;
        $approved_by = $event->approved_by;
        $through = $event->through;
        $forwarded_by = $event->forwarded_by;
        $forwarded_to = $event->forwarded_to;

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->action = 'Document Forwarded';
        $log->remarks = 'Document '.$subject.' has been Forwarded through '.$through.'. Approved by: '.$approved_by.' and Remarks: '.$remarks;
        return $log->save();

    }
}
