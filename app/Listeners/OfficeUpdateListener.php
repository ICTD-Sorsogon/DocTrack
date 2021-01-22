<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OfficeUpdateListener
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
        $office_name = $event->request_obj->name;
        $office_code = $event->request_obj->office_code;
        $old_values = json_encode($event->request_obj);
        $data = json_encode($event->old_values);

        $log = new Log;
        $log->user_id = $event->user_id;
        $log->new_values = $data;
        $log->original_values = $old_values;
        $log->action = 'Office Updated';
        $log->remarks = 'Office has been Updated with new Office Name of : '.$office_name.' and Office Code of: '.$office_code;
        return $log->save();
    }
}
