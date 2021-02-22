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

        switch ($document->status) {
            case 'created':
                $action = $document->wasRecentlyCreated;
                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = auth()->user()->id;
                $notification->office_id = 37;
                $notification->action = $action ? 'created' : 'updated';
                $notification->status = 0;
                $notification->message = "Document {$document->subject} with {$document->tracking_code} tracking code has been {$notification->action} by " . auth()->user()->fullname . ".";
                $notification->save();
            break;

            case 'acknowledged':
                foreach ($document->destination_office_id as $key => $destination) {
                    $notification = new Notification();
                    $notification->document_id = $document->id;
                    $notification->user_id = auth()->user()->id;
                    $notification->office_id = $destination;
                    $notification->action = 'acknowledged';
                    $notification->status = 0;
                    $notification->message = "You got new document {$document->subject} with {$document->tracking_code} tracking code from {$document->origin_office->name}.";
                    $notification->save();
                }

                    $notification = new Notification();
                    $notification->document_id = $document->id;
                    $notification->user_id = auth()->user()->id;
                    $notification->office_id = $document->origin_office->id;
                    $notification->action = 'acknowledged';
                    $notification->status = 0;
                    $notification->message = "Your document {$document->subject} with {$document->tracking_code} tracking code has been acknowledged.";
                    $notification->save();
            break;

            case 'holdreject':

                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = auth()->user->id;
                $notification->office_id = $document->origin_office->id;
                $notification->sender_name = auth()->user()->fullname;
                $notification->status = 0;
                $notification->message = "Your document {$document->subject} with {$document->tracking_code} tracking code has been hold/reject by" . auth()->user()->fullname . ".";
                $notification->save();

            break;

            case 'forwarded':
                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = auth()->user()->id;
                $notification->office_id = 37;
                $notification->action = 'forwarded';
                $notification->sender_name = auth()->user()->fullname;
                $notification->status = 0;
                $notification->message = "Document {$document->subject} with {$document->tracking_code} tracking code has been forwarded by " . auth()->user()->fullname . " to {$document->destination->first()->name}.";
                $notification->save();

                $notification = new Notification();
                $notification->document_id = $document->id;
                $notification->user_id = auth()->user()->id;
                $notification->action = 'forwarded';
                $notification->office_id = $document->originating_office;
                $notification->status = 0;
                $notification->message = "Your document {$document->subject} with {$document->tracking_code} tracking code has been forwarded by " . auth()->user()->fullname . " to {$document->destination->first()->name}.";
                $notification->save();
            break;

            case 'received':
                    $notification = new Notification();
                    $notification->document_id = $document->id;
                    $notification->user_id = auth()->user()->id;
                    $notification->office_id = $document->origin_office->id;
                    $notification->action = 'recieved';
                    $notification->status = 0;
                    $notification->message = "Your document {$document->subject} with {$document->tracking_code} tracking code has been recieved.";
                    $notification->save();

                if($document->origin_office->office_code != "DO"){
                    $notification = new Notification();
                    $notification->document_id = $document->id;
                    $notification->user_id = auth()->user()->id;
                    $notification->office_id = 37;
                    $notification->action = 'recieved';
                    $notification->status = 0;
                    $notification->message = "Document {$document->subject} with {$document->tracking_code} tracking code has been recieved.";
                    $notification->save();
                }
            break;
        }
    }
}
