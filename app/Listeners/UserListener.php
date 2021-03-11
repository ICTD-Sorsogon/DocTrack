<?php

namespace App\Listeners;

use App\Events\UserEvent;
use App\Models\Log;
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
        switch ($event->type) {
            case 'create':
                $username = json_decode($event->request_obj)->username;
                $role_id = json_decode($event->request_obj)->role_id;
                $last_name = json_decode($event->request_obj)->last_name;
                $first_name = json_decode($event->request_obj)->first_name;
                $middle_name = json_decode($event->request_obj)->middle_name;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->new_values = $event->request_obj;
                $log->action = 'User create';
                $log->remarks = 'New account has been successfully created for '.$last_name.', '.$first_name.',
                    '.$middle_name.'  with username of '.$username.' and role id of '.$role_id;

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
                $log->action = 'User update';
                $log->remarks = 'Account has been successfully updated for '.$last_name.', '.$first_name.',
                    '.$middle_name.'  with username of '.$username;
                return $log->save();
            break;

            case 'delete':
                $username = $event->request_obj->username;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'User delete';
                $log->remarks = 'User has been successfully deleted with username of : '.$username;
                return $log->save();
            break;
        }
    }
}
