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
        switch($event->type){
            case 'create':
                $office_name = $event->request_obj->name;
                $office_code = $event->request_obj->office_code;
                $data = json_encode($event->request_obj);
        
                $log = new Log();
                $log->user_id = $event->user_id;
                $log->new_values = $data;
                $log->action = 'Office Created';
                $log->remarks = 'New Office has been Created with Office Name of : '.$office_name.' and Office Code of: '.$office_code;
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
                $log->action = 'Office Updated';
                $log->remarks = 'Office has been Updated with new Office Name of : '.$name.' and Office Code of: '.$code;
                return $log->save();
            break;

            case 'delete':
                $office_name = $event->request_obj->name;
                $office_code = $event->request_obj->office_code;

                $log = new Log;
                $log->user_id = $event->user_id;
                $log->action = 'Office Deleted';
                $log->remarks = 'Office has been Deleted with Office Name of : '.$office_name.' and Office Code of: '.$office_code;
                return $log->save();
            break;

            case 'import':
                $tab = json_encode($event->request_obj);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Office Imported';
                $log->remarks = 'Office Imported with Sheet name of : '.$tab;
                return $log->save();
            break;
        }
    }
}
