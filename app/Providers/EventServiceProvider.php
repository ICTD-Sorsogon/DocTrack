<?php

namespace App\Providers;

use App\Events\DocumentDeleteEvent;
use App\Events\DocumentUpdateEvent;
use App\Events\NewDocumentHasAddedEvent;
use App\Events\OfficeCreateEvent;
use App\Events\OfficeUpdateEvent;
use App\Events\OfficeDeleteEvent;
use App\Listeners\DocumentDeleteListener;
use App\Listeners\DocumentUpdateListener;
use App\Listeners\InsertDocumentListener;
use App\Listeners\OfficeCreateListener;
use App\Listeners\OfficeDeleteListener;
use App\Listeners\OfficeUpdateListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NewDocumentHasAddedEvent::class => [
            InsertDocumentListener::class,
        ],
        DocumentUpdateEvent::class => [
            DocumentUpdateListener::class,
        ],
        DocumentDeleteEvent::class => [
            DocumentDeleteListener::class,
        ],

        //Office
            OfficeCreateEvent::class => [
            OfficeCreateListener::class,
        ],
            OfficeUpdateEvent::class => [
            OfficeUpdateListener::class,
        ],
            OfficeDeleteEvent::class => [
            OfficeDeleteListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
