<?php

namespace App\Listeners;

use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Models\User;
class NotificationListener
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
    public function handle(NotificationEvent $event)
    {
        extract(get_object_vars($event));

        foreach($document as $doc){
            if(Notification::find($doc->id)->expire()){
                foreach($doc->document_recipient as $doc_recip){
                    $notification = new Notification();
                    $notification->document_id = $doc->id;
                    $notification->user_id = User::find($doc->originating_office);
                    $notification->office_id = $doc_recip->destination_office;
                    $notification->action = 'Reminder';
                    $notification->status = 0;
                    $notification->message = "You have unproccess documents. Please check your document lists.";
                    $notification->save();
                }
            }
        }
    }
}
