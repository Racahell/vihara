<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DiscordLog;
use App\Models\Donation;
use App\Models\LoginLog;

class NotificationController extends Controller
{
    public function index()
    {
        $summary = [
            'donasi_pending' => Donation::where('verification_status', 'pending')->count(),
            'login_gagal_hari_ini' => LoginLog::where('successful', false)->whereDate('logged_in_at', now()->toDateString())->count(),
            'event_discord_hari_ini' => DiscordLog::whereDate('created_at', now()->toDateString())->count(),
            'aktivitas_hari_ini' => ActivityLog::whereDate('created_at', now()->toDateString())->count(),
        ];

        $recentNotifications = ActivityLog::query()
            ->whereIn('action', ['create_donation', 'verify_donation', 'checkin', 'walkin', 'update_user', 'delete_user'])
            ->latest()
            ->limit(20)
            ->get();

        $discordNotifications = DiscordLog::latest('created_at')->limit(20)->get();

        return view('admin.notifications', [
            'summary' => $summary,
            'recentNotifications' => $recentNotifications,
            'discordNotifications' => $discordNotifications,
        ]);
    }
}
