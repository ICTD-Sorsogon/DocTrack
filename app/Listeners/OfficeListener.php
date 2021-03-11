<?php

namespace App\Listeners;

use App\Events\OfficeEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OfficeListener
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
    public function handle(OfficeEvent $event)
    {
        switch ($event->type) {
            case 'create':
                $office_name = $event->request_obj->name;
                $office_code = $event->request_obj->office_code;
                $data = json_encode($event->request_obj);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->new_values = $data;
                $log->action = 'Office create';
                $log->remarks = 'New office has been successfully created with office name of : '.$office_name.
                    ' and office code of: '.$office_code;
                return $log->save();
            break;

            case 'update':
                $old_values = json_encode($event->request_obj);
                $data = json_encode($event->old_values);
                $name = json_decode($data)->name;
                $code = json_decode($data)->office_code;

                $log = new Log;
                $log->user_id = $event->user_id;
                $log->new_values = $data;
                $log->original_values = $old_values;
                $log->action = 'Office update';
                $log->remarks = 'Office has been successfully updated with new office name of :
                    '.$name.' and office code of: '.$code;
                return $log->save();
            break;

            case 'delete':
                $office_name = $event->request_obj->name;
                $office_code = $event->request_obj->office_code;

                $log = new Log;
                $log->user_id = $event->user_id;
                $log->action = 'Office delete';
                $log->remarks = 'Office has been successfully deleted with office name of :
                    '.$office_name.' and office code of: '.$office_code;
                return $log->save();
            break;

            case 'import':
                $tab = json_encode($event->request_obj);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Office imported';
                $log->remarks = 'Office has been successfully imported with sheet name of : '.$tab;
                return $log->save();
            break;
        }
    }
}
