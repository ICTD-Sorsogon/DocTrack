<?php

namespace App\Listeners;

use App\Events\DocumentEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentListener
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
    public function handle(DocumentEvent $event)
    {
        switch($event->type){
            case 'create':
                $subject = $event->request_obj->subject;
                $data = json_encode($event->request_obj);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->new_values = $data;
                $log->action = 'Document Created';
                $log->remarks = 'New Document has been Created with Subject of : '.$subject;
                return $log->save();
            break;

            case 'update':
                $subject = $event->request_obj->subject;
                $old_values = json_encode($event->old_values);
                $old_subject = $event->old_values->subject;
                $data = json_encode($event->request_obj);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->new_values = $data;
                $log->original_values = $old_values;
                $log->action = 'Document Updated';
                $log->remarks = 'Document has been Update from : '.$old_subject.' to '.$subject;
                return $log->save();

            break;

            case 'acknowledge':
                $remarks = $event->old_values;
                $subject = $event->request_obj;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document Acknowledge';
                $log->remarks = 'Document '.$subject.' has been Acknowledge. Remarks:'.$remarks;
                return $log->save();
            break;

            case 'holdreject':
                $status = $event->request_obj;
                $subject = $event->old_values;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document Hold or Reject';
                $log->remarks = 'Document '.$subject.' is '.$status;
                return $log->save();
            break;
            
            case 'terminate':
                $remarks = $event->old_values;
                $subject = $event->request_obj;
                $approved_by = $event->approved_by;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document Terminate';
                $log->remarks = 'Document '.$subject.' has been Terminate. Approved by: '.$approved_by.'and Remarks: '.$remarks;
                return $log->save();
            break;

            case 'forward':
                $remarks = json_encode($event->request_obj->documentRemarks);
                $subject = json_encode($event->request_obj->subject);
                $approved_by = json_encode($event->request_obj->approved_by);
                $through = json_encode($event->request_obj->through);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document Forwarded';
                $log->remarks = 'Document '.$subject.' has been Forwarded through '.$through.'. Approved by: '.$approved_by.' and Remarks: '.$remarks;
                return $log->save();
            break;

            case 'receive':
                $remarks = json_encode($event->request_obj->documentRemarks);
                $subject = json_encode($event->request_obj->subject);
                $approved_by = json_encode($event->request_obj->approved_by);
                $through = json_encode($event->request_obj->through);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document Received';
                $log->remarks = 'Document '.$subject.' has been Received through '.$through.'. Approved by: '.$approved_by.' and Remarks: '.$remarks;
                return $log->save();
            break;
        }
    }
}
