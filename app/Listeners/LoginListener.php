<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LoginListener
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
    public function handle(LoginEvent $event)
    {
        switch($event->type){
            case 'login':
                $username = json_encode($event->request_obj->username);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'User Login';
                $log->remarks = 'Username: '.$username.' has logged in. Time: '.Carbon::now();
                return $log->save();
            break;

            case 'logout':
                $username = json_encode($event->request_obj);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'User Logout';
                $log->remarks = 'Username: '.$username.' has logged out. Time: '.Carbon::now();
                return $log->save();
            break;
        }
    }
}
