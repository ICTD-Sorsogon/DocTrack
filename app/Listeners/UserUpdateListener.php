<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserUpdateListener
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
        $old_values = json_encode($event->request_obj);
        $data = json_encode($event->old_values);
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
    }
}
