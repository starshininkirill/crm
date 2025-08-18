<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Inertia::share([
            'user_permissions' => function () {
                $user = auth()->user();

                if ($user) {
                    return [
                        'permissions' => $user->getAllPermissions()->pluck('name'),
                        'roles' => $user->roles->pluck('name'),
                    ];
                }

                return [];
            },
            'permissions' => function () {
                return [
                    'permissions' => Permission::get(),
                    'roles' => Role::get(),
                ];
            },
        ]);
    }
}
