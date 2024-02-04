<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Ldap\Scopes\OnlyDesbUsers;
use App\Models\EForm;
use App\Models\User;
use App\Policies\EFormPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Eform::class => EFormPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        \LdapRecord\Models\ActiveDirectory\User::addGlobalScope(
            new OnlyDesbUsers
        );
    }
}
