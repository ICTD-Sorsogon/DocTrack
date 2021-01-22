<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OfficeCreateListener
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
        $office_name = $event->request->name;
        $office_code = $event->request->office_code;

        $log = new Log;
        $log->user_id = $event->user_id;
        $log->action = 'Office Created';
        $log->remarks = 'New Office has been Created with Office Name of : '.$office_name.' and Office Code of: '.$office_code;
        return $log->save();
    }
}
