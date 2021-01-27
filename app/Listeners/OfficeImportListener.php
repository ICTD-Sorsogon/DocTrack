<?php

namespace App\Listeners;

use App\Events\OfficeImportEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OfficeImportListener
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
    public function handle(OfficeImportEvent $event)
    {
        $tab = json_encode($event->tab);

        $log = new Log();
        $log->user_id = $event->user_id;
        $log->action = 'Office Imported';
        $log->remarks = 'Office Imported with Sheet name of : '.$tab;
        return $log->save();
    }
}
