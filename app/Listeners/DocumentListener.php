<?php

namespace App\Listeners;

use App\Events\DocumentEvent;
use App\Models\Log;
use App\Models\Office;
use App\Models\TrackingRecord;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope\MethodScopeSniff;
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

        $remarks = $document->remarks;
        $subject = $document->subject;
        $tracking_code = $document->tracking_code;

        switch($document->status){
            case 'created':
                if($document->wasRecentlyCreated){
                    $destinationOffce = '';
                    $document_length = count(json_decode($document->destination_office_id));

                    for($index = 0; $index < $document_length; $index++){
                        $destinationOffce .= json_decode($document->destination)[$index]->name . ', ';
                    }

                        $data_object = (object) array(
                            'subject' => $subject,
                            'sender_name' => $document->sender_name,
                            'remarks' => $remarks,
                            'attachment_page_count' => $document->attachment_page_count,
                            'destination_office_id' => substr($destinationOffce, 0, -2),
                            'document_type_id' => $document->document_type_id,
                            'page_count' => $document->page_count,
                        );

                        $data = json_encode($data_object);

                        $log = new Log();
                        $log->user_id = auth()->user()->id;
                        $log->new_values = $data;
                        $log->action = 'Document created';
                        $log->remarks = "Document {$subject} with tracking code of {$tracking_code} was created by " . auth()->user()->fullname . ".";
                        return $log->save();
                }

                else if(!$document->wasRecentlyCreated){
                    $old = $document->getOriginal();
                    $new_destinationOffices = $this->destinationOffice($document, 'new');
                    $old_destinationOffices = $this->destinationOffice($old, 'old');

                    $new_object = (object) array(
                        'subject' => $document->subject,
                        'sender_name' => $document->sender_name,
                        'remarks' => $document->remarks,
                        'attachment_page_count' => $document->attachment_page_count,
                        'destination_office_id' => substr($new_destinationOffices, 0, -2),
                        'document_type_id' => $document->document_type_id,
                        'page_count' => $document->page_count,
                    );

                    $old_object = (object) array(
                        'subject' => $old['subject'],
                        'sender_name' => $old['sender_name'],
                        'remarks' => $old['remarks'],
                        'attachment_page_count' => $old['attachment_page_count'],
                        'destination_office_id' => $old_destinationOffices,
                        'document_type_id' => $old['document_type_id'],
                        'page_count' => $old['page_count'],
                    );

                    $new_data = json_encode($new_object);
                    $old_data = json_encode($old_object);

                    $log = new Log();
                    $log->user_id = auth()->user()->id;
                    $log->new_values = $new_data;
                    $log->original_values = $old_data;
                    $log->action = 'Document update';
                    $log->remarks = "Document {$old['subject']} with tracking code of {$document->tracking_code}
                        has been successfully updated by " . auth()->user()->fullname . ".";
                    return $log->save();
                }
            break;
        }

        $tracking_records = last($document->tracking_records->toArray());
        $through = $tracking_records['through'];
        $approved_by = $tracking_records['approved_by'];

        switch($tracking_records['action']){
            case 'acknowledged':
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document acknowledged';
                $log->remarks = "{$subject} with tracking code of {$tracking_code} was successfully acknowledged with remarks: {$remarks} by " . auth()->user()->fullname . ".";
                return $log->save();
            break;

            case 'on hold':
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document hold';
                $log->remarks = "{$subject} with tracking code of {$tracking_code} was hold.";
                return $log->save();
            break;

            case 'released':
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document release';
                $log->remarks = "{$subject} with tracking code of {$tracking_code} has been released.";
                return $log->save();
            break;

            case 'terminated':
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document terminated';
                $log->remarks = "{$subject} with tracking code of {$tracking_code} was successfully terminated and approved by: {$approved_by} with remarks: {$remarks}";
                return $log->save();
            break;

            case 'forwarded':
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document forwarded';
                $log->remarks = "{$subject} with tracking code of {$tracking_code} was successfully forwarded through {$through} and approved by: {$approved_by} with remarks of: {$remarks}";
                return $log->save();
            break;

            case 'received':
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document received';
                $log->remarks = "{$subject} wast tracking code of {$tracking_code} was successfully received through {$through} and approved by: {$approved_by} with remarks of: {$remarks}";
                return $log->save();
            break;
        }
    }

    public function destinationOffice($document, $type) {
        $destinationOffce = '';

        if($type == 'old'){
            $document_length = count(json_decode($document['destination_office_id']));
            for($index = 0; $index < $document_length; $index++){
                $office_id = json_decode($document['destination_office_id'])[$index];
                $office = Office::find($office_id);
                $destinationOffce .= $office->name . ', ';
            }
        }
        if($type == 'new'){
            $document_length = count(json_decode($document->destination_office_id));
            for($index = 0; $index < $document_length; $index++){
                $destinationOffce .= json_decode($document->destination)[$index]->name . ', ';
            }
        }

        return $destinationOffce;
    }
}
