<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserDeleteListener
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
        $username = $event->user->username;

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->action = 'User Deleted';
        $log->remarks = 'User has been Deleted with Username of : '.$username;
        return $log->save();
    }
}
