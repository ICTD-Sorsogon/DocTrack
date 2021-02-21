<?php

namespace App\Listeners;

use App\Events\DocumentEvent;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Office;
use App\Models\Personnel;
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
        $originating_notif = User::where('office_id', json_decode($document->originating_office))->get();
        $user_office_id = User::where('office_id', $document->originating_office)->get();
        $docket_offices = User::where('office_id', 37)->get();
        $office = Office::where('id', auth()->user()->office_id)->first();

        switch ($document->status) {
            case 'created':
                $docket_office = User::where('office_id', 37)->get();

                $document_old = $document->getOriginal();
                $document_new = $document->getAttributes();
                
                
                if($document->wasRecentlyCreated){

                    if($document->originating_office != 37){
                        foreach($docket_offices as $docket_office){
                            $notification = new Notification();
                            $notification->document_id = $document->id;
                            $notification->user_id = $docket_office->id;
                            $notification->office_id = 37;
                            $notification->tracking_code = $document->tracking_code;
                            $notification->subject = $document->subject;
                            $notification->status = 0;
                            $notification->message = 'Document '.$document->subject.' has been created.';
                            $notification->save();
                        }
                    }


                } 
                // else {
                //     foreach ($notify_user as $key => $value) {
                //         $notification = new Notification();
                //         $notification->document_id = $document->id;
                //         $notification->user_id = $value['id'];
                //         $notification->office_id = $value['office_id'];
                //         $notification->tracking_code = $document->tracking_code;
                //         $notification->subject = $document->subject;
                //         $notification->status = 0;
                //         $notification->message = 'Document '.$document_old['subject'].' has been updated to '.$document_new['subject'].'.';
                //         $notification->save();
                //     }

                //     if($document->originating_office != 37){
                //         foreach($docket_offices as $docket_office){
                //             $notification = new Notification();
                //             $notification->document_id = $document->id;
                //             $notification->user_id = $docket_office->id;
                //             $notification->office_id = 37;
                //             $notification->tracking_code = $document->tracking_code;
                //             $notification->subject = $document->subject;
                //             $notification->status = 0;
                //             $notification->message = 'Document '.$document_old['subject'].' has been updated to '.$document_new['subject'].'.';
                //             $notification->save();
                //         }
                //     }
                    
                // }
            break;
            
            case 'acknowledged':
                foreach ($notify_user as $key => $value) {
                    $notification = new Notification();
                    $notification->document_id = $document->id;
                    $notification->user_id = $value->id;
                    $notification->office_id = $value->office_id;
                    $notification->tracking_code = $document->tracking_code;
                    $notification->subject = $document->subject;
                    $notification->status = 0;
                    $notification->message = 'Document '.$document->subject.' has been acknowledged and will be deliver to your office.';
                    $notification->save();
                }

                // $sender_notif = User::where('id', $sender_id)->first();
                //     $notification->document_id = $document->id;
                //     $notification->user_id = $sender_notif->id;
                //     $notification->office_id = $sender_notif->office_id;
                //     $notification->sender_name = $name->first_name . ', ' . $name->middle_name . ', '
                //     . $name->last_name . ' ' . $name->suffix;
                //     $notification->status = 0;
                //     $notification->message = 'Document '.$document->subject.' has been acknowledged.';
                //     $notification->save();
                
                
                    foreach ($originating_notif as $key => $value) {
                        $notification = new Notification();
                        $notification->document_id = $document->id;
                        $notification->user_id = $value['id'];
                        $notification->office_id = $value['office_id'];
                        $notification->tracking_code = $document->tracking_code;
                        $notification->subject = $document->subject;
                        $notification->status = 0;
                        $notification->message = 'Document created by your office '.$document->subject.' has been acknowledged.';
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

            case 'terminated':

                if($document->originating_office != 37){
                    foreach($docket_offices as $docket_office){
                        $notification = new Notification();
                        $notification->document_id = $document->id;
                        $notification->user_id = $docket_office->id;
                        $notification->office_id = 37;
                        $notification->tracking_code = $document->tracking_code;
                        $notification->subject = $document->subject;
                        $notification->status = 0;
                        $notification->message = 'Document created by '.$office->name.' - '.$document->subject.' has been terminated.';
                        $notification->save();
                    }
                }

                foreach ($originating_notif as $key => $value) {

                    if($office->id != $value['office_id']){
                        $notification = new Notification();
                        $notification->document_id = $document->id;
                        $notification->user_id = $value['id'];
                        $notification->office_id = $value['office_id'];
                        $notification->tracking_code = $document->tracking_code;
                        $notification->subject = $document->subject;
                        $notification->status = 0;
                        $notification->message = 'Document created by your office '.$document->subject.' has been terminated by ' . $office->name. '.';
                        $notification->save();
                    }
                }
            break;

            case 'forwarded':
                $forwarded_data = last($document->tracking_records->toArray());

                $forwarded_by = Office::find($forwarded_data['forwarded_by']);
                $through = $forwarded_data['through'];

                $destination_office_arr = json_decode($document->destination_office_id);
                $destination_office = Office::find(end($destination_office_arr));
                $notify_user = User::whereIn('office_id', [$destination_office->id])->get();

                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = $notify_user[0]->id;
                $notification->office_id = end($destination_office_arr);
                $notification->tracking_code = $document->tracking_code;
                $notification->subject = $document->subject;
                $notification->status = 0;
                $notification->message = 'Document '.$document->subject.' is forwarded to your office by '
                    . $forwarded_by->name . ' through ' . $through;
                $notification->save(); 
                
                if($document->originating_office != 37){
                    foreach($docket_offices as $docket_office){
                        $notification = new Notification();
                        $notification->document_id = $document->id;
                        $notification->user_id = $docket_office->id;
                        $notification->office_id = 37;
                        $notification->tracking_code = $document->tracking_code;
                        $notification->subject = $document->subject;
                        $notification->status = 0;
                        $notification->message = 'Document '.$document->subject.' is forwarded to '
                            . $destination_office->name . ' from ' . $forwarded_by->name . ' through ' . $through;
                        $notification->save();
                    }
                }

            break;

            case 'received':
                $receiver_fullname = Auth::user()->last_name. ', ' . Auth::user()->first_name. ' ' . Auth::user()->middle_name;
                $office_received = Office::find(Auth::user()->office_id);

                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = $user_office_id[0]->id;
                $notification->office_id = $user_office_id[0]->office_id;
                $notification->tracking_code = $document->tracking_code;
                $notification->subject = $document->subject;
                $notification->status = 0;
                $notification->message = 'Document '.$document->subject.' has been received by '. $receiver_fullname . ' at ' . $office_received->name;
                $notification->save(); 
                
                if($document->originating_office != 37){
                    foreach($docket_offices as $docket_office){
                        $notification = new Notification();
                        $notification->document_id = $document->id;
                        $notification->user_id = $docket_office->id;
                        $notification->office_id = 37;
                        $notification->tracking_code = $document->tracking_code;
                        $notification->subject = $document->subject;
                        $notification->status = 0;
                        $notification->message = 'Document '.$document->subject.' has been received by '. $receiver_fullname . ' at ' . $office_received->name;
                        $notification->save();
                    }
                }
            break;
        }
    }
}
