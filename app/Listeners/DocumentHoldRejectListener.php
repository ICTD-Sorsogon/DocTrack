<?php

namespace App\Listeners;

use App\Events\DocumentHoldRejectEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentHoldRejectListener
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
    public function handle(DocumentHoldRejectEvent $event)
    {


        $status = $event->status;
        $subject = $event->subject;

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->action = 'Document Hold or Reject';
        $log->remarks = 'Document '.$subject.' is '.$status;
        return $log->save();
    }
}
