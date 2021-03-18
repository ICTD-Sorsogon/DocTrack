<?php

namespace App\Listeners;

use App\Events\NotificationEvent;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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

        $seven_days = 604800000; // 7 Days
        $fifteen_days = 1296000000; // 15 Days
        $thirty_days = 2592000000 ; // 30 Days

        $updated_at = Carbon::parse($notification->document->updated_at)->getPreciseTimestamp(3);
        $expired_at_7 = Carbon::now()->getPreciseTimestamp(3) < $updated_at+$seven_days;
        $expired_at_15 = Carbon::now()->getPreciseTimestamp(3) < $updated_at+$fifteen_days;
        $expired_at_30 = Carbon::now()->getPreciseTimestamp(3) < $updated_at+$thirty_days;

        if($notification->document->acknowledged == 1 && !$notification->document->trashed()){
            if($expired_at_7 && $notification->priority_level == 1){
                $notification_db = new Notification();
                $notification_db->document_id = $notification->document->document_id;
                $notification_db->user_id = $notification->users->id;
                $notification_db->office_id = $notification->users->office_id;
                $notification_db->action = 'Reminder';
                $notification_db->status = 0;
                $notification_db->message = "You have unproccess documents for 7 days. Please check your document lists.";
                $notification_db->save();
            }
            else if($expired_at_15 && $notification->priority_level == 2){
                $notification_db = new Notification();
                $notification_db->document_id = $notification->document->document_id;
                $notification_db->user_id = $notification->users->id;
                $notification_db->office_id = $notification->users->office_id;
                $notification_db->action = 'Reminder';
                $notification_db->status = 0;
                $notification_db->message = "You have unproccess documents for 15 days. Please check your document lists.";
                $notification_db->save();

            }
            else if($expired_at_30 && $notification->priority_level == 3){
                $notification_db = new Notification();
                $notification_db->document_id = $notification->document->document_id;
                $notification_db->user_id = $notification->users->id;
                $notification_db->office_id = $notification->users->office_id;
                $notification_db->action = 'Reminder';
                $notification_db->status = 0;
                $notification_db->message = "You have unproccess documents for 30 days. Please check your document lists.";
                $notification_db->save();
            }
        }
    }
}
