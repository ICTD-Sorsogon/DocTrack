<?php

namespace App\Providers;

use App\Events\AccountFullnameUpdateEvent;
use App\Events\AccountPasswordUpdateEvent;
use App\Events\AccountUsernameUpdateEvent;
use App\Events\DocumentDeleteEvent;
use App\Events\DocumentUpdateEvent;
use App\Events\NewDocumentHasAddedEvent;
use App\Events\OfficeCreateEvent;
use App\Events\OfficeUpdateEvent;
use App\Events\OfficeDeleteEvent;
use App\Events\UserCreateEvent;
use App\Events\UserUpdateEvent;
use App\Events\UserDeleteEvent;
use App\Listeners\AccountFullnameListener;
use App\Listeners\AccountPasswordListener;
use App\Listeners\AccountUsernameListener;
use App\Listeners\DocumentDeleteListener;
use App\Listeners\DocumentUpdateListener;
use App\Listeners\InsertDocumentListener;
use App\Listeners\OfficeCreateListener;
use App\Listeners\OfficeDeleteListener;
use App\Listeners\OfficeUpdateListener;
use App\Listeners\UserCreateListener;
use App\Listeners\UserUpdateListener;
use App\Listeners\UserDeleteListener;
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
        ],

        //User 
            UserCreateEvent::class => [
            UserCreateListener::class,
        ],
            UserUpdateEvent::class => [
            UserUpdateListener::class,
        ],
            UserDeleteEvent::class => [
            UserDeleteListener::class,
        ],

        // Account
            AccountFullnameUpdateEvent::class => [
            AccountFullnameListener::class,
        ],
            AccountUsernameUpdateEvent::class => [
            AccountUsernameListener::class,
        ],
            AccountPasswordUpdateEvent::class => [
            AccountPasswordListener::class,
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
