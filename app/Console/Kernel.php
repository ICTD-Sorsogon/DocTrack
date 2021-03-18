<?php

namespace App\Console;

use App\Events\NotificationEvent;
use App\Models\Document;
use App\Models\DocumentRecipient;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $seven_days = 604800000; // 7 Days
            $notification = Notification::get();
            foreach ($notification as $notif){
                $is_terminated = DocumentRecipient::find($notif->document_id)->onlyTrashed();
                $updated_at = Carbon::parse($notif->updated_at)->getPreciseTimestamp(3);
                $expired_at_7 = Carbon::now()->getPreciseTimestamp(3) < $updated_at+$seven_days;

                if($notif->action = 'Reminder' && $notif->status == 1 && $expired_at_7){
                    $notif->delete();
                }else if($notif->status == 1 && $expired_at_7 && $is_terminated) {
                    $notif->delete();
                }

            }
        })->weeklyOn(1, '8:00');

        $schedule->call(function () {
            $document = Document::withTrashed();
            $incoming_trashed = $document->with(['incoming_trashed'])->get();

            foreach ($incoming_trashed as $doc){
                foreach($doc->incoming_trashed as $incoming){
                    if(!$incoming->deleted_at){
                        $originating_office = $doc->originating_office;
                        $document = $incoming;
                        $priority_level = $doc->priority_level;
                        $users = User::find($originating_office);

                        $notification_data = (object)
                            array('users' => $users,
                            'document' => $document,
                            'priority_level' => $priority_level
                        );

                        event(new NotificationEvent($notification_data));
                    }

                }

            }
        })->weeklyOn(1, '8:00');


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
