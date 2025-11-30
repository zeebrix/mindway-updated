<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // We will register our model policies here later.
        // Example: \App\Models\Session::class => \App\Policies\SessionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * --------------------------------------------------------------------
         * Define Application Access Gates
         * --------------------------------------------------------------------
         * These gates determine if a user can access entire sections of the
         * application (portals). They are used as middleware in routes.
         */

        // Gate for Admin Portal Access
        Gate::define('access-admin-panel', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate for Counsellor Portal Access
        Gate::define('access-counsellor-panel', function (User $user) {
            return $user->role === 'counsellor';
        });

        // Gate for Program (Employer) Portal Access
        Gate::define('access-program-panel', function (User $user) {
            return $user->role === 'program';
        });

        /**
         * --------------------------------------------------------------------
         * Define Super-Admin Gate
         * --------------------------------------------------------------------
         * This gate grants a super-admin all permissions automatically.
         * It runs before any other authorization check.
         */
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });
    }
}
