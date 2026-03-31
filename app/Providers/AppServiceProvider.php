<?php

namespace App\Providers;

use App\Services\BankMutation\BankMutationProviderInterface;
use App\Services\BankMutation\JsonFileBankMutationProvider;
use App\Services\BankMutation\NullBankMutationProvider;
use App\Models\WebsiteSetting;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
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
        $this->app->bind(BankMutationProviderInterface::class, function () {
            $provider = (string) config('services.bank_mutation.provider', 'null');
            if ($provider === 'json_file') {
                return new JsonFileBankMutationProvider(
                    (string) config('services.bank_mutation.disk', 'local'),
                    (string) config('services.bank_mutation.path', 'bank-mutations/incoming.json')
                );
            }

            return new NullBankMutationProvider();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('components.pagination');
        Paginator::defaultSimpleView('components.pagination');
        RedirectIfAuthenticated::redirectUsing(function () {
            $user = auth()->user();

            if ($user?->hasRole('umat') && Route::has('umat.dashboard')) {
                return route('umat.dashboard');
            }

            if ($user?->hasPermission('dashboard.view') && Route::has('dashboard')) {
                return route('dashboard');
            }

            if (Route::has('umat.dashboard')) {
                return route('umat.dashboard');
            }

            return Route::has('guest.home') ? route('guest.home') : '/';
        });

        View::composer('*', function ($view) {
            static $siteName = null;
            static $sharedWebsiteSettings = null;
            if ($siteName === null) {
                try {
                    $siteName = WebsiteSetting::query()
                        ->where('key', 'website_name')
                        ->value('value') ?: config('app.name');
                } catch (QueryException $e) {
                    $siteName = config('app.name');
                }
            }

            if ($sharedWebsiteSettings === null) {
                $defaults = [
                    'website_name' => $siteName ?: config('app.name'),
                    'website_logo_path' => '',
                    'website_favicon_path' => '',
                    'company_description' => 'Ruang informasi kegiatan dan layanan umat.',
                    'contact_phone' => '',
                    'contact_email' => '',
                ];

                try {
                    $fromDb = WebsiteSetting::query()
                        ->whereIn('key', array_keys($defaults))
                        ->pluck('value', 'key')
                        ->toArray();
                    $sharedWebsiteSettings = array_merge($defaults, $fromDb);
                } catch (QueryException $e) {
                    $sharedWebsiteSettings = $defaults;
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
            $view->with('websiteSettings', $sharedWebsiteSettings);
        });
    }
}
