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
        $allowedTabs = $roleSlug === 'superadmin'
            ? ['activity', 'login', 'discord', 'error']
            : ['activity', 'login'];

        $tab = $request->string('tab')->toString();
        if (! in_array($tab, $allowedTabs, true)) {
            $tab = 'activity';
        }

        $activityLogs = $tab === 'activity' ? ActivityLog::latest()->paginate(25, ['*'], 'activity_page') : null;
        $loginLogs = $tab === 'login' ? LoginLog::latest('logged_in_at')->paginate(25, ['*'], 'login_page') : null;
        $discordLogs = in_array('discord', $allowedTabs, true) && $tab === 'discord'
            ? DiscordLog::latest('created_at')->paginate(25, ['*'], 'discord_page')
            : null;
        $errorLogs = in_array('error', $allowedTabs, true) && $tab === 'error'
            ? $this->readErrorLogs($request)
            : null;

        return view('admin.logs', [
            'tab' => $tab,
            'allowedTabs' => $allowedTabs,
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

    private function readErrorLogs(Request $request): LengthAwarePaginator
    {
        $path = storage_path('logs/laravel.log');
        if (! file_exists($path)) {
            return new LengthAwarePaginator([], 0, 30, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        $lines = collect(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        $tail = $lines->reverse()->take(400)->values();

        $page = max((int) $request->query('error_page', 1), 1);
        $perPage = 30;
        $items = $tail->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator($items, $tail->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
            'pageName' => 'error_page',
        ]);
    }
}
