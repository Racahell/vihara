<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DiscordLog;
use App\Models\Donation;
use App\Models\LoginLog;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $summary = [
            'donasi_pending' => Donation::where('verification_status', 'pending')->count(),
            'login_gagal_hari_ini' => LoginLog::where('successful', false)->whereDate('logged_in_at', now()->toDateString())->count(),
            'event_discord_hari_ini' => DiscordLog::whereDate('created_at', now()->toDateString())->count(),
            'aktivitas_hari_ini' => ActivityLog::whereDate('created_at', now()->toDateString())->count(),
        ];

        $recentNotifications = ActivityLog::query()
            ->whereIn('action', ['create_donation', 'verify_donation', 'checkin', 'walkin', 'update_user', 'delete_user'])
            ->latest()
            ->paginate($perPage, ['*'], 'recent_page')
            ->withQueryString();

        $discordNotifications = DiscordLog::latest('created_at')
            ->paginate($perPage, ['*'], 'discord_page')
            ->withQueryString();

        return view('admin.notifications', [
            'summary' => $summary,
            'perPage' => $perPage,
            'recentNotifications' => $recentNotifications,
            'discordNotifications' => $discordNotifications,
        ]);
    }
}
