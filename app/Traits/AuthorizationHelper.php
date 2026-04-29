<?php

namespace App\Traits;

use Illuminate\Support\Facades\Gate;

trait AuthorizationHelper
{
    /**
     * Check if the authenticated user can perform a gate action
     */
    public function authorize(string $gate): bool
    {
        return auth()->check() && Gate::allows($gate);
    }

    /**
     * Check if the authenticated user cannot perform a gate action
     */
    public function cannot(string $gate): bool
    {
        return auth()->guest() || Gate::denies($gate);
    }

    /**
     * Get the current user's role
     */
    public function getUserRole(): ?string
    {
        return auth()->user()?->role;
    }

    /**
     * Check if user has a specific role
     */
    public function userHasRole(string|array $role): bool
    {
        return auth()->check() && auth()->user()->hasRole($role);
    }
}
