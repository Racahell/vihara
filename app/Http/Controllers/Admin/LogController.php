<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DiscordLog;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $roleSlug = (string) $request->user()?->roles()->value('slug');
        $perPage = (int) $request->integer('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
        $allowedTabs = $roleSlug === 'superadmin'
            ? ['activity', 'login', 'discord', 'error']
            : ['activity', 'login'];

        $tab = $request->string('tab')->toString();
        if (! in_array($tab, $allowedTabs, true)) {
            $tab = 'activity';
        }

        $activityLogs = $tab === 'activity'
            ? ActivityLog::latest()->paginate($perPage, ['*'], 'activity_page')->withQueryString()
            : null;
        $loginLogs = $tab === 'login'
            ? LoginLog::latest('logged_in_at')->paginate($perPage, ['*'], 'login_page')->withQueryString()
            : null;
        $discordLogs = in_array('discord', $allowedTabs, true) && $tab === 'discord'
            ? DiscordLog::latest('created_at')->paginate($perPage, ['*'], 'discord_page')->withQueryString()
            : null;
        $errorLogs = in_array('error', $allowedTabs, true) && $tab === 'error'
            ? $this->readErrorLogs($request, $perPage)
            : null;

        return view('admin.logs', [
            'tab' => $tab,
            'allowedTabs' => $allowedTabs,
            'perPage' => $perPage,
            'activityLogs' => $activityLogs,
            'loginLogs' => $loginLogs,
            'discordLogs' => $discordLogs,
            'errorLogs' => $errorLogs,
        ]);
    }

    public function activity(Request $request)
    {
        return redirect()->route('admin.logs.index', ['tab' => 'activity'] + $request->query());
    }

    public function login(Request $request)
    {
        return redirect()->route('admin.logs.index', ['tab' => 'login'] + $request->query());
    }

    private function readErrorLogs(Request $request, int $perPage): LengthAwarePaginator
    {
        $path = storage_path('logs/laravel.log');
        if (! file_exists($path)) {
            return new LengthAwarePaginator([], 0, $perPage, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        $lines = collect(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        $tail = $lines->reverse()->take(400)->values();

        $page = max((int) $request->query('error_page', 1), 1);
        $items = $tail->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator($items, $tail->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
            'pageName' => 'error_page',
        ]);
    }
}
