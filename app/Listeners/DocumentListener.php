<?php

namespace App\Listeners;

use App\Events\DocumentEvent;
use App\Models\Log;
use App\Models\TrackingRecord;
use Carbon\Carbon;
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
        
        extract(get_object_vars($event));
        if($document->wasRecentlyCreated){
            foreach($document->destination as $office){
                TrackingRecord::create([
                    'action' => 'created',
                    'destination' => $office->id,
                    'document_id' => $document->id,
                    'touched_by' => auth()->user()->id,
                    'remarks' => $document->remarks,
                    'last_touched' => Carbon::now()
                ]);
            };
        }
        return false;
        $type = ['edited', 'created', 'received', 'forwarded', 'processing', 'on hold', 'rejected', 'terminated', 'acknowledged'];
        $message = 'Document has been successfully';



        
        switch($event->type){
            case 'create':
                $subject = $event->request_obj->subject;
                $data = json_encode($event->request_obj);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->new_values = $data;
                $log->action = 'Document create';
                $log->remarks = 'New document has been successfully created with subject of : '.$subject;
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
                $log->action = 'Document update';
                $log->remarks = 'Document has been successfully updated from : '.$old_subject.' to '.$subject;
                return $log->save();

            break;

            case 'acknowledge':
                $remarks = $event->old_values;
                $subject = $event->request_obj;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document acknowledge';
                $log->remarks = 'Document '.$subject.' has been successfully acknowledge with remarks:'.$remarks;
                return $log->save();
            break;

            case 'holdreject':
                $status = $event->request_obj;
                $subject = $event->old_values;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document hold or reject';
                $log->remarks = 'Document '.$subject.' is '.$status;
                return $log->save();
            break;

            case 'terminate':
                $remarks = $event->old_values;
                $subject = $event->request_obj;
                $approved_by = $event->approved_by;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document terminate';
                $log->remarks = 'Document '.$subject.' has been successfully terminated and approved by: '.$approved_by.'with remarks: '.$remarks;
                return $log->save();
            break;

            case 'forward':
                $remarks = json_encode($event->request_obj->documentRemarks);
                $subject = json_encode($event->request_obj->subject);
                $approved_by = json_encode($event->request_obj->approved_by);
                $through = json_encode($event->request_obj->through);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document forward';
                $log->remarks = 'Document '.$subject.' has been successfully forwarded through '.$through.'. and approved by: '.$approved_by.' with remarks: '.$remarks;
                return $log->save();
            break;

            case 'receive':
                $remarks = json_encode($event->request_obj->documentRemarks);
                $subject = json_encode($event->request_obj->subject);
                $approved_by = json_encode($event->request_obj->approved_by);
                $through = json_encode($event->request_obj->through);

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document receive';
                $log->remarks = 'Document '.$subject.' has been successfully received through '.$through.'. and approved by: '.$approved_by.' and remarks: '.$remarks;
                return $log->save();
            break;
        }
    }
}
