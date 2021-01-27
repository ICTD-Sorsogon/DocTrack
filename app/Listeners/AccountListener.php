<?php

namespace App\Listeners;

use App\Events\AccountEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AccountListener
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
    public function handle(AccountEvent $event)
    {
        switch($event->type){
            case 'fullname':
                $old_values = json_encode($event->old_values);
                $data = json_encode($event->request_obj);
                $first_name = json_encode($event->request_obj->first_name);
                $middle_name = json_encode($event->request_obj->middle_name);
                $last_name = json_encode($event->request_obj->last_name);
                $suffix = json_encode($event->request_obj->suffix);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->new_values = $data;
                $log->original_values = $old_values;
                $log->action = 'Account Fullname Updated';
                $log->remarks = 'Account Fullname has been Updated to : '.$last_name.', '.$first_name.', '.$middle_name.' '.$suffix;
                return $log->save();
            break;

            case 'password':
                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Account Password Updated';
                $log->remarks = 'Account Password has been Updated';
                return $log->save();
            break;

            case 'username':
                $username = json_encode($event->request_obj->username);
                $data = json_encode($event->old_values);
                $old_values = json_encode($event->old_values);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Account Username Updated';
                $log->remarks = 'Account Username has been Updated from : '.$old_values.' to '.$username;
                return $log->save();
            break;
        }
    }
}
