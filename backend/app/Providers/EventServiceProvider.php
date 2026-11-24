<?php

namespace App\Providers;

use App\Events\ExampleEvent;
use App\Listeners\ExampleListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            // Future registration listeners
        ],
        ExampleEvent::class => [
            ExampleListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
