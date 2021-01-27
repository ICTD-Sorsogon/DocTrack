<?php

namespace App\Listeners;

use App\Events\UserEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserEvent $event)
    {
        switch($event->type){
            case 'create':
                $username = json_decode($event->request_obj)->username;
                $role_id = json_decode($event->request_obj)->role_id;
                $last_name = json_decode($event->request_obj)->last_name;
                $first_name = json_decode($event->request_obj)->first_name;
                $middle_name = json_decode($event->request_obj)->middle_name;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->new_values = $event->request_obj;
                $log->action = 'User Created';
                $log->remarks = 'New Account has been Created for '.$last_name.', '.$first_name.', '.$middle_name.'  with Username of '.$username.' and Role ID of '.$role_id;

                return $log->save();
            break;
            
            case 'update':
                $old_values = json_encode($event->old_values);
                $data = json_encode($event->request_obj);
                $last_name = json_decode($data)->last_name;
                $first_name = json_decode($data)->first_name;
                $middle_name = json_decode($data)->middle_name;
                $username = json_decode($data)->username;

                $log = new Log;
                $log->user_id = $event->user_id;
                $log->new_values = $data;
                $log->original_values = $old_values;
                $log->action = 'User Updated';
                $log->remarks = 'Account has been Updated for '.$last_name.', '.$first_name.', '.$middle_name.'  with Username of '.$username;
                return $log->save();
            break;

            case 'delete':
                $username = $event->request_obj->username;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'User Deleted';
                $log->remarks = 'User has been Deleted with Username of : '.$username;
                return $log->save();
            break;
        }
    }
}
