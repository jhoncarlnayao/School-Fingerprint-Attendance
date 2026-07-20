<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
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
        // Without this, Laravel's "guest" middleware doesn't know where to
        // send an already-logged-in user who hits /login — it falls back to
        // '/', which redirects right back to '/login' (infinite redirect
        // loop). Send them to their role's dashboard instead, same place
        // AuthenticatedSessionController::store() sends them after login.
        RedirectIfAuthenticated::redirectUsing(function () {
            $user = Auth::user();

            return $user ? $user->dashboardRoute() : '/';
        });
    }
}
