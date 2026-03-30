<?php

namespace App\Providers;

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
            $user = auth()->user();
            $roleSlug = $user?->roles()->value('slug');
            $menuGroups = [];
            $activeMarked = false;

            $rawGroups = $roleSlug ? config("menu.$roleSlug", []) : [];
            foreach ($rawGroups as $group) {
                $preparedItems = [];

                foreach (($group['items'] ?? []) as $item) {
                    $routeName = $item['route'] ?? null;
                    $isCurrentRoute = $routeName ? request()->routeIs($routeName) : false;
                    $isActive = $isCurrentRoute && ! $activeMarked;

                    if ($isActive) {
                        $activeMarked = true;
                    }

                    $preparedItems[] = [
                        'label' => $item['label'] ?? '-',
                        'route' => $routeName,
                        'href' => $routeName && Route::has($routeName) ? route($routeName) : '#',
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
        });
    }
}

