<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ThreadHasNewReply::class => [
            'App\Listeners\NotifyThreadSubscribers',
        ],
        ThreadReceivedNewReply::class => [
            'App\Listeners\NotifyMentionedUsers'
        ],
        Registered::class => [
            'App\Listeners\SendEmailConfirmationRequest'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
