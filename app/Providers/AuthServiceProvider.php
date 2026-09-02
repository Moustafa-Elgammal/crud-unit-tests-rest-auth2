<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Passport 12+ registers its own routes automatically; Passport::routes() was removed.
        if (method_exists(Passport::class, 'enableImplicitGrant')) {
            Passport::enableImplicitGrant();
        }

        // The Postman collection authenticates via the OAuth password grant, which
        // Passport 11+ disables by default.
        Passport::enablePasswordGrant();
    }
}
