<?php

namespace App\Listeners;

use App\Events\DocumentTerminateEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentTerminateListener
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
    public function handle(DocumentTerminateEvent $event)
    {
        $remarks = $event->remarks;
        $subject = $event->subject;
        $approved_by = $event->approved_by;

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->action = 'Document Terminate';
        $log->remarks = 'Document '.$subject.' has been Terminate. Approved by: '.$approved_by.'and Remarks: '.$remarks;
        return $log->save();
    }
}
