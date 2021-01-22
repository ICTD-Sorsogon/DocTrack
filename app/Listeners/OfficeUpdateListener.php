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
        $old_values = json_encode($event->request_obj);
        $data = json_encode($event->old_values);
        $name = json_decode($data)->name;
        $code = json_decode($data)->office_code;

        $log = new Log;
        $log->user_id = $event->user_id;
        $log->new_values = $data;
        $log->original_values = $old_values;
        $log->action = 'Office Updated';
        $log->remarks = 'Office has been Updated with new Office Name of : '.$name.' and Office Code of: '.$code;
        return $log->save();
    }
}
