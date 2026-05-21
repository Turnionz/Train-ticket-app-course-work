<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin-only', function (User $user) {
            return $user->role === User::$role[0];
        });

        Gate::define('operator-level', function (User $user) {
            return in_array($user->role, [User::$role[0], User::$role[1]]);
        });

        Gate::define('employee', function (User $user) {
            return $user->role === User::$role[2] && $user->employee_id !== null;
        });

        Gate::define('employee-level', function (User $user) {
            return in_array($user->role, [User::$role[0], User::$role[1], User::$role[2]]);
        });

        Gate::define('user', function (User $user) {
            return $user->role === User::$role[3];
        });

        Gate::define('owner', function (User $user, $model) {
            return $user->id === $model->user_id;
        });

        Gate::define('user-level', function (User $user) {
            return in_array($user->role, User::$role);
        });
    }
}
