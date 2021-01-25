<?php

namespace App\Listeners;

use App\Events\AccountFullnameUpdateEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AccountFullnameListener
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
    public function handle(AccountFullnameUpdateEvent $event)
    {
        $old_values = json_encode($event->request_obj);
        $data = json_encode($event->old_values);
        $first_name = json_decode($data)->first_name;
        $middle_name = json_decode($data)->middle_name;
        $last_name = json_decode($data)->last_name;
        $suffix = json_decode($data)->suffix;

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->new_values = $data;
        $log->original_values = $old_values;
        $log->action = 'Account Fullname Updated';
        $log->remarks = 'Account Fullname has been Updated to : '.$last_name.', '.$first_name.', '.$middle_name.' '.$suffix;
        return $log->save();
    }
}
