<?php

namespace App\World\Workbench\Providers;

use App\World\Workbench\Policies\CountryPolicy;
use App\World\Workbench\Policies\CurrencyPolicy;
use App\World\Workbench\Policies\PostPolicy;
use App\World\Workbench\Policies\RolePolicy;
use Eclipse\World\Models\Country;
use Eclipse\World\Models\Currency;
use Eclipse\World\Models\Post;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Country::class => CountryPolicy::class,
        Currency::class => CurrencyPolicy::class,
        Post::class => PostPolicy::class,
        Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
