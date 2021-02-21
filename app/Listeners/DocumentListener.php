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

        // return false;
        // $type = ['edited', 'created', 'received', 'forwarded', 'processing', 'on hold', 'rejected', 'terminated', 'acknowledged'];
        // $message = 'Document has been successfully';

        if(!$document->wasRecentlyCreated && 
            $document->status != 'acknowledged' &&
            $document->status != 'received' &&
            $document->status != 'forwarded' &&
            $document->status != 'terminated'
            ){
            $new = $document;
            $old = $document->getOriginal();
            $new_destinationOffices = $this->destinationOffice($new, 'new');
            $old_destinationOffices = $this->destinationOffice($old, 'old');

            $new_object = (object) array(
                'subject' => $new->subject,
                'sender_name' => $new->sender_name,
                'remarks' => $new->remarks,
                'attachment_page_count' => $new->attachment_page_count,
                'destination_office_id' => $new_destinationOffices,
                'document_type_id' => $new->document_type_id,
                'page_count' => $new->page_count,
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
            $log->remarks = 'Document has been successfully updated from : '.$old['subject'].' to '.$new->subject;
            return $log->save();
        }



        switch($document->status){
            case 'created':
                if($document->wasRecentlyCreated){
                    $destinationOffce = '';
                    $document_length = count(json_decode($document->destination_office_id));
        
                    for($index = 0; $index < $document_length; $index++){
                        $destinationOffce .= json_decode($document->destination)[$index]->name . ', ';
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
                }
            break;

            case 'acknowledged':
                $remarks = $document->remarks;
                $subject = $document->subject;
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document acknowledged';
                $log->remarks = 'Document '.$subject.' has been successfully acknowledge with remarks:'.$remarks;
                return $log->save();
            break;

            case 'holdreject':
                $status = $document->status;
                $subject = $document->subject;

                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document hold or reject';
                $log->remarks = 'Document '.$subject.' is '.$status;
                return $log->save();
            break;

            case 'terminated':
                $subject = $document->subject;
                $approved_by = $document->approved_by;
                $remarks = $document->remarks;

                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document terminate';
                $log->remarks = 'Document '.$subject.' has been successfully terminated and approved by: 
                    '.$approved_by.'with remarks: '.$remarks;
                return $log->save();
            break;

            case 'forwarded':
                $forwarded_data = last($document->tracking_records->toArray());

                $remarks = $document->remarks;
                $subject = $document->subject;
                $approved_by = $forwarded_data['approved_by'];
                $through = $forwarded_data['through'];
                
                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document forward';
                $log->remarks = 'Document '.$subject.' has been successfully forwarded through '.$through.
                ' and approved by: '.$approved_by.' with remarks of: '.$remarks;
                return $log->save();
            break;

            case 'received':
                $received_data = last($document->tracking_records->toArray());

                $through = $received_data['through'];
                $approved_by = $received_data['approved_by'];
                $remarks = $document->remarks;
                $subject = $document->subject;

                $log = new Log();
                $log->user_id = auth()->user()->id;
                $log->action = 'Document receive';
                $log->remarks = 'Document '.$subject.' has been successfully received through '.$through.
                ' and approved by: '.$approved_by.' with remarks of: '.$remarks;
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
