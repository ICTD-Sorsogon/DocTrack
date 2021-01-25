<?php

namespace App\Listeners;

use App\Events\AccountUsernameUpdateEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AccountUsernameListener
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
    public function handle(AccountUsernameUpdateEvent $event)
    {
        $old_values = $event->request_obj;
        $data = json_encode($event->old_values);
        $username = json_decode($data)->username;

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->action = 'Account Username Updated';
        $log->remarks = 'Account Username has been Updated from : '.$old_values.' to '.$username;
        return $log->save();
    }
}
