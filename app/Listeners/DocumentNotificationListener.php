<?php

namespace App\Listeners;

use App\Events\DocumentEvent;
use App\Models\Document;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DocumentNotificationListener
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
        switch ($event->type) {
            case 'create':
                $sender_id = $event->request_obj->sender_name;
                // $notify_user = User::whereIn('office_id', [$event->office_id])->get();
                // dd($sender_id);
                $document = Document::all()->find($event->document_id);

                $notify_user = User::whereIn('office_id', [$event->office_id])->get();
                // $test = '' ;
                foreach ($notify_user as $key => $value) {
                    $notification = new Notification();
                    $notification->document_id = $event->document_id;
                    $notification->user_id = $value->id;
                    $notification->office_id = $event->office_id;
                    $notification->status = 0;
                    $notification->message = 'Document '.$document->subject.' has been created.';
                    $notification->save();
                }
                // dd($test);
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
                $log->remarks = 'Document '.$subject.' has been successfully terminated and approved by: 
                    '.$approved_by.'with remarks: '.$remarks;
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
                $log->remarks = 'Document '.$subject.' has been successfully forwarded through '.$through.'. 
                    and approved by: '.$approved_by.' with remarks: '.$remarks;
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
                $log->remarks = 'Document '.$subject.' has been successfully received through '.$through.'. 
                    and approved by: '.$approved_by.' and remarks: '.$remarks;
                return $log->save();
            break;
        }
    }
}
