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
        $sender_id = $document->sender_name;
        // dd($sender_id);
        $name = User::all()->find($sender_id) ?? $sender_id;
        $document_data = Document::all()->find($document->id);
        $user_office_id = User::where('office_id', $document->originating_office)->get();

        switch ($document->status) {
            case 'created':
                $action = $document->wasRecentlyCreated;
                $notification = new Notification();
                $notification->document_id = $document_data->id;
                $notification->user_id = auth()->user()->id;
                $notification->office_id = 37;
                $notification->action = $action ? 'created' : 'updated';
                $notification->status = 0;
                $notification->message = "Document {$document_data->subject} with {$document_data->tracking_code} tracking code has been {$notification->action} by " . auth()->user()->fullname . ".";
                $notification->save();
            break;

            case 'acknowledged':
                foreach ($document->destination_office_id as $key => $destination) {
                    $notification = new Notification();
                    $notification->document_id = $document_data->id;
                    $notification->user_id = auth()->user()->id;
                    $notification->office_id = $destination;
                    $notification->action = 'acknowledged';
                    $notification->status = 0;
                    $notification->message = "You got new document {$document_data->subject} with {$document_data->tracking_code} tracking code from {$document->origin_office->name}.";
                    $notification->save();
                }

                    $notification = new Notification();
                    $notification->document_id = $document_data->id;
                    $notification->user_id = auth()->user()->id;
                    $notification->office_id = $document->origin_office->id;
                    $notification->action = 'acknowledged';
                    $notification->status = 0;
                    $notification->message = "Your document {$document_data->subject} with {$document_data->tracking_code} tracking code has been acknowledged.";
                    $notification->save();
            break;

            case 'holdreject':

                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = auth()->user->id;
                $notification->office_id = $document->origin_office->id;
                $notification->sender_name = auth()->user()->fullname;
                $notification->status = 0;
                $notification->message = "Your document {$document_data->subject} with {$document_data->tracking_code} tracking code has been hold/reject by" . auth()->user()->fullname . ".";
                $notification->save();


                $status = $event->request_obj;
                $subject = $event->old_values;

                $log = new Log();
                $log->user_id = $event->user_id;
                $log->action = 'Document hold or reject';
                $log->remarks = 'Document '.$subject.' is '.$status;
                return $log->save();
            break;

            case 'forwarded':
                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = auth()->user()->id;
                $notification->office_id = 37;
                $notification->action = 'forwarded';
                $notification->sender_name = auth()->user()->fullname;
                $notification->status = 0;
                $notification->message = "Document {$document_data->subject} with {$document_data->tracking_code} tracking code has been forwarded by " . auth()->user()->fullname . " to {$document->destination->first()->name}.";
                $notification->save();

                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = auth()->user()->id;
                $notification->action = 'forwarded';
                $notification->office_id = $document->destination_office_id->first();
                $notification->status = 0;
                $notification->message = "Your document {$document_data->subject} with {$document_data->tracking_code} tracking code has been forwarded by " . auth()->user()->fullname . " to {$document->destination->first()->name}.";
                $notification->save();
            break;

            case 'received':
                    $notification = new Notification();
                    $notification->document_id = $document_data->id;
                    $notification->user_id = auth()->user()->id;
                    $notification->office_id = $document->origin_office->id;
                    $notification->action = 'recieved';
                    $notification->status = 0;
                    $notification->message = "Your document {$document_data->subject} with {$document_data->tracking_code} tracking code has been recieved.";
                    $notification->save();

                if($document->origin_office->office_code != "DO"){
                    $notification = new Notification();
                    $notification->document_id = $document_data->id;
                    $notification->user_id = auth()->user()->id;
                    $notification->office_id = 37;
                    $notification->action = 'recieved';
                    $notification->status = 0;
                    $notification->message = "Document {$document_data->subject} with {$document_data->tracking_code} tracking code has been recieved.";
                    $notification->save();
                }
            break;
        }
    }
}
