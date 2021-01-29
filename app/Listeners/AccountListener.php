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
                $log->action = 'Account fullname update';
                $log->remarks = 'Account fullname has been successfully updated to : '.$last_name.', '.$first_name.', '.$middle_name.' '.$suffix;
                return $log->save();
            break;

            case 'password':
                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Account password update';
                $log->remarks = 'Account password has been successfully updated';
                return $log->save();
            break;

            case 'username':
                $username = json_encode($event->request_obj->username);
                $data = json_encode($event->old_values);
                $old_values = json_encode($event->old_values);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Account username update';
                $log->remarks = 'Account username has been successfully updated from : '.$old_values.' to '.$username;
                return $log->save();
            break;
        }
    }
}
