<?php

namespace App\Listeners;

use App\Events\DocumentEvent;
use App\Models\Document;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

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
        extract(get_object_vars($event));
        $notify_user = User::whereIn('office_id', json_decode($document->getAttributes()['destination_office_id']))->get();
        
        switch ($document->status) {
            case 'created':
                $sender_id = $document->sender_name;
                $name = User::all()->find($sender_id);
                $docket_office = User::where('office_id', 37)->get();
                $document = Document::all()->find($document->id);
                    $notification = new Notification();
                    $notification->document_id = $document->id;
                    $notification->user_id = $docket_office[0]->id;
                    $notification->office_id = $docket_office[0]->office_id;
                    $notification->sender_name = $name->first_name . ', ' . $name->middle_name . ', '
                    . $name->last_name . ' ' . $name->suffix;
                    $notification->status = 0;
                    $notification->message = 'Document '.$document->subject.' has been created.';
                    $notification->save();
                
                // foreach ($notify_user as $key => $value) {
                //     $notification = new Notification();
                //     $notification->document_id = $document->id;
                //     $notification->user_id = $value->id;
                //     $notification->office_id = $value->office_id;
                //     $notification->sender_name = $name->first_name . ', ' . $name->middle_name . ', '
                //     . $name->last_name . ' ' . $name->suffix;
                //     $notification->status = 0;
                //     $notification->message = 'Document '.$document->subject.' has been created.';
                //     $notification->save();
                // }
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

            case 'acknowledged':
                $sender_id = $document->sender_name;
                $name = User::all()->find($sender_id);
                $document = Document::all()->find($document->id);
                
                foreach ($notify_user as $key => $value) {
                    $notification = new Notification();
                    $notification->document_id = $document->id;
                    $notification->user_id = $value->id;
                    $notification->office_id = $value->office_id;
                    $notification->sender_name = $name->first_name . ', ' . $name->middle_name . ', '
                    . $name->last_name . ' ' . $name->suffix;
                    $notification->status = 0;
                    $notification->message = 'Document '.$document->subject.' has been acknowledged and will be deliver to your office.';
                    $notification->save();
                }
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

            case 'received':
                $sender_id = $document->sender_name;
                $name = User::all()->find($sender_id);
                $document = Document::all()->find($document->id);
                $user_office_id = User::where('office_id', $document->originating_office)->get();
                $receiver_fullname = Auth::user()->last_name. ', ' . Auth::user()->first_name. ' ' . Auth::user()->middle_name;
                
                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = $user_office_id[0]->id;
                $notification->office_id = $document->originating_office;
                $notification->sender_name = $name->first_name . ', ' . $name->middle_name . ', '
                . $name->last_name . ' ' . $name->suffix;
                $notification->status = 0;
                $notification->message = 'Document '.$document->subject.' has been received by '. $receiver_fullname;
                $notification->save();

            break;
        }
    }
}
