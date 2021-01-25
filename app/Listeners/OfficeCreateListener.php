<?php

namespace App\Listeners;

use App\Events\OfficeCreateEvent;
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
    public function handle(OfficeCreateEvent $event)
    {
        $office_name = $event->request_obj->name;
        $office_code = $event->request_obj->office_code;
        $data = json_encode($event->request_obj);

        $log = new Log;
        $log->user_id = $event->user_id;
        $log->new_values = $data;
        $log->action = 'Office Created';
        $log->remarks = 'New Office has been Created with Office Name of : '.$office_name.' and Office Code of: '.$office_code;
        return $log->save();
    }
}
