<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Register policies in future modules.
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
