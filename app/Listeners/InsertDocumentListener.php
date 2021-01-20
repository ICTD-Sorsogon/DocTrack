<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InsertDocumentListener
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
    public function handle($event)
    {
        $subject = $event->request->subject;

        $log = new Log;
        $log->user_id = $event->user_id;
        $log->action = 'Document Created';
        $log->remarks = 'New Document has been Created with Subject of : '.$subject;
        return $log->save();
    }
}
