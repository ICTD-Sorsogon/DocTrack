<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AccountPasswordListener
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
        $log = new Log();
        $log->user_id = $event->user_id;
        $log->action = 'Account Password Updated';
        $log->remarks = 'Account Password has been Updated';
        return $log->save();
    }
}
