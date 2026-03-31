<?php

namespace App\Providers;

use App\Models\WebsiteSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
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
        Paginator::defaultView('components.pagination');
        Paginator::defaultSimpleView('components.pagination');

        View::composer('*', function ($view) {
            static $siteName = null;
            if ($siteName === null) {
                try {
                    $siteName = WebsiteSetting::query()
                        ->where('key', 'website_name')
                        ->value('value') ?: config('app.name');
                } catch (QueryException $e) {
                    $siteName = config('app.name');
                }
            }

            $user = auth()->user();
            $roleSlug = $user?->roles()->value('slug');
            $userRoleSlugs = $user ? $user->roles()->pluck('slug')->all() : [];
            $menuGroups = [];
            $activeMarked = false;

            $rawGroups = $roleSlug ? config("menu.$roleSlug", []) : [];
            foreach ($rawGroups as $group) {
                $preparedItems = [];

                foreach (($group['items'] ?? []) as $item) {
                    $routeName = $item['route'] ?? null;
                    if (! $routeName || ! Route::has($routeName)) {
                        continue;
                    }

                    $route = Route::getRoutes()->getByName($routeName);
                    if (! $route) {
                        continue;
                    }

                    $routeRoleMiddleware = collect($route->gatherMiddleware())
                        ->first(fn (string $middleware) => str_starts_with($middleware, 'role:'));
                    if ($routeRoleMiddleware) {
                        $allowedRoles = collect(explode(',', substr($routeRoleMiddleware, strlen('role:'))))
                            ->map(fn (string $role) => trim($role))
                            ->filter()
                            ->values()
                            ->all();
                        if ($allowedRoles !== [] && ! collect($userRoleSlugs)->intersect($allowedRoles)->isNotEmpty()) {
                            continue;
                        }
                    }

                    $requiredPermission = (string) config("access_control.route_permissions.$routeName", '');
                    if ($requiredPermission !== '' && $user && ! $user->hasPermission($requiredPermission)) {
                        continue;
                    }

                    $isCurrentRoute = $routeName ? request()->routeIs($routeName) : false;
                    $isActive = $isCurrentRoute && ! $activeMarked;

                    if ($isActive) {
                        $activeMarked = true;
                    }

                    $preparedItems[] = [
                        'label' => $item['label'] ?? '-',
                        'route' => $routeName,
                        'href' => route($routeName),
                        'is_active' => $isActive,
                    ];
                }

                if ($preparedItems !== []) {
                    $menuGroups[] = [
                        'title' => $group['title'] ?? 'Menu',
                        'items' => $preparedItems,
                    ];
                }
            }

            $view->with('currentRoleSlug', $roleSlug);
            $view->with('sidebarMenuGroups', $menuGroups);
            $view->with('siteName', $siteName);
        });
    }
}

