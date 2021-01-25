<?php

namespace App\Listeners;

use App\Events\OfficeDeleteEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OfficeDeleteListener
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
    public function handle(OfficeDeleteEvent $event)
    {
        $office_name = $event->office->name;
        $office_code = $event->office->office_code;

        $log = new Log;
        $log->user_id = $event->user_id;
        $log->action = 'Office Deleted';
        $log->remarks = 'Office has been Deleted with Office Name of : '.$office_name.' and Office Code of: '.$office_code;
        return $log->save();
    }
}
