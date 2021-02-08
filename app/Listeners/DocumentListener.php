<?php

namespace App\Listeners;

use App\Events\DocumentEvent;
use App\Models\Log;
use App\Models\TrackingRecord;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Symfony\Component\VarDumper\VarDumper;

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
        foreach($document->destination_office_id as $office){
            TrackingRecord::create([
                'action' => 'created',
                'destination' => $office->id,
                'document_id' => $document->id,
                'touched_by' => auth()->user()->id,
                'remarks' => $document->remarks,
                'last_touched' => Carbon::now()
            ]);
        };
        // return false;
        // $type = ['edited', 'created', 'received', 'forwarded', 'processing', 'on hold', 'rejected', 'terminated', 'acknowledged'];
        // $message = 'Document has been successfully';


        switch($document->status){
            case 'created':

        // if (!$document->id) {
        //     $request_obj = '{
        //         "subject":"' . $request->subject . '",
        //         "sender_name":"' . $request->sender_name . '",
        //         "remarks":"' . $request->remarks . '",
        //         "attachment_page_count":"' . $request->attachment_page_count . '",
        //         "destination_office_id":"' . $request->destination_office_id . '",
        //         "document_type_id":"' . $request->document_type_id . '",
        //         "page_count":"' . $request->page_count . '"}';

        //     $user_id = Auth::user()->id;
        //     event(new DocumentEvent($user_id, json_decode($request_obj), null,null, 'create'));

        // } else {
        //     $old_values = Document::select(
        //     'attachment_page_count','destination_office_id',
        //     'document_type_id','id','originating_office','page_count','remarks','sender_name',
        //     'subject','tracking_code'
        //     )->where('id', $request->id)->get();
        //     $request_obj = '{
        //         "subject":"' . $request->subject . '",
        //         "sender_name":"' . $request->sender_name . '",
        //         "remarks":"' . $request->remarks . '",
        //         "attachment_page_count":"' . $request->attachment_page_count . '",
        //         "destination_office_id":"' . $request->destination_office_id . '",
        //         "document_type_id":"' . $request->document_type_id . '",
        //         "page_count":"' . $request->page_count . '"}';


        //     $user_id = Auth::user()->id;
        //     event(new DocumentEvent($user_id ,json_decode($request_obj), json_decode($old_values[0]), null, 'update'));

        // }


            $destinationOffce = '';
            foreach ($document->destination_office_id as $myoffice) {
                $destinationOffce .= $myoffice->name . ', ';
            }

            $data_object = (object) array(
                'subject' => $document->subject,
                'sender_name' => $document->sender_name,
                'remarks' => $document->remarks,
                'attachment_page_count' => $document->attachment_page_count,
                'destination_office_id' => $destinationOffce,
                'document_type_id' => $document->document_type_id,
                'page_count' => $document->page_count,
            );


                $subject = $document->subject;
                $data = json_encode($data_object);

                $log = new Log();
                $log->user_id = auth()->user()->id;
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

            case 'deleting':
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
