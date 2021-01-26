<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserCreateListener
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
        $username = json_decode($event->collection)->username;
        $role_id = json_decode($event->collection)->role_id;
        $last_name = json_decode($event->collection)->last_name;
        $first_name = json_decode($event->collection)->first_name;
        $middle_name = json_decode($event->collection)->middle_name;

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->new_values = $event->collection;
        $log->action = 'User Created';
        $log->remarks = 'New Account has been Created for '.$last_name.', '.$first_name.', '.$middle_name.'  with Username of '.$username.' and Role ID of '.$role_id;

        return $log->save();
    }
}
