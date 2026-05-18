<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        // Role Gates
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manager', function (User $user) {
            return $user->isManager();
        });

        Gate::define('cashier', function (User $user) {
            return $user->isCashier();
        });

        // Admin Modules
        Gate::define('view-admin-dashboard', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-sales-report', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-reports', function (User $user) {
            return $user->isAdmin();
        });

        // Manager Modules
        Gate::define('view-manager-dashboard', function (User $user) {
            return $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-categories', function (User $user) {
            return $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-employees', function (User $user) {
            return $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-suppliers', function (User $user) {
            return $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-inventory', function (User $user) {
            return $user->isManager();
        });

        Gate::define('view-sales-orders', function (User $user) {
            return $user->isCashier() || $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-manager-sales-report', function (User $user) {
            return $user->isManager();
        });

        Gate::define('view-customers', function (User $user) {
            return $user->isCashier() || $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-raw-materials', function (User $user) {
            return $user->isManager();
        });

        Gate::define('view-production-in', function (User $user) {
            return $user->isManager();
        });

        Gate::define('view-production-out', function (User $user) {
            return $user->isManager();
        });

        Gate::define('view-purchases', function (User $user) {
            return $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-manager-reports', function (User $user) {
            return $user->isManager();
        });

        Gate::define('view-discounts', function (User $user) {
            return $user->isManager() || $user->isAdmin();
        });

        // Cashier Modules
        Gate::define('view-cashier-dashboard', function (User $user) {
            return $user->isCashier() || $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-products', function (User $user) {
            return $user->isCashier() || $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-cashier-sales-orders', function (User $user) {
            return $user->isCashier() || $user->isManager() || $user->isAdmin();
        });

        Gate::define('view-cashier-customers', function (User $user) {
            return $user->isCashier() || $user->isManager() || $user->isAdmin();
        });
    }
}
