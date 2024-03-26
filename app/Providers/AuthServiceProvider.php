<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Company;
use App\Models\Freelancer;
use App\Models\Role;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use Laravel\Passport\Passport;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
         'App\Models' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function($user){
            return $user->role_id == Role::ROLE_ADMINISTRATOR;
        });

        Gate::define('isCompany', function($user){
            return $user->role_id == Role::ROLE_USER &&  ($user->userable_type == Company::class);
        });

        Gate::define('isFreelancer', function($user){
            return $user->role_id == Role::ROLE_USER &&  ($user->userable_type == Freelancer::class);
        });
    }
}
