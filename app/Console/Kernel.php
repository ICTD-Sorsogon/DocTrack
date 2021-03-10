<?php

namespace App\Console;

use App\Events\NotificationEvent;
use App\Models\Document;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        $schedule->call(function () {
            $document_delete = Notification::where('status', 1)
            ->orWhereHas('document', function($query){$query
            ->whereNotNull('deleted_at');})
            ->get();

            foreach($document_delete as $to_delete){
                $days = Carbon::now()->diffInDays($to_delete->updated_at);

                if($days > 7){
                    $to_delete->delete();
                }
            }
        })->weeklyOn(1, '8:00');

        $schedule->call(function () {
            $document_recipient = Document::with(['document_recipient'])
            ->whereHas('document_recipient', function($query){
                $query->where('acknowledged', 1)->whereNull('deleted_at');
            })->get();

            event(new NotificationEvent($document_recipient));
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
