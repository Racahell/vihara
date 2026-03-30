<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\Donation;
use App\Models\DonationCategory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        $stats = [
            'kegiatan_aktif' => Activity::where('is_active', true)->count(),
            'pendaftaran_total' => ActivityRegistration::count(),
            'hadir_hari_ini' => ActivityRegistration::whereDate('checked_in_at', now()->toDateString())->count(),
            'donasi_berhasil' => Donation::where('verification_status', 'approved')->sum('amount'),
        ];

        $monthExpr = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', donated_at)"
            : "DATE_FORMAT(donated_at, '%Y-%m')";

        $monthlyDonations = Donation::query()
            ->selectRaw("{$monthExpr} as month_key, SUM(amount) as total")
            ->where('verification_status', 'approved')
            ->whereNotNull('donated_at')
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->limit(12)
            ->get();

        $categoryBreakdown = DonationCategory::query()
            ->leftJoin('donations', function ($join): void {
                $join->on('donation_categories.id', '=', 'donations.donation_category_id')
                    ->where('donations.verification_status', '=', 'approved');
            })
            ->select('donation_categories.name', DB::raw('COALESCE(SUM(donations.amount), 0) as total'))
            ->groupBy('donation_categories.id', 'donation_categories.name')
            ->orderBy('donation_categories.name')
            ->get();

        return view('shared.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'monthlyDonationLabels' => $monthlyDonations->pluck('month_key'),
            'monthlyDonationValues' => $monthlyDonations->pluck('total'),
            'categoryLabels' => $categoryBreakdown->pluck('name'),
            'categoryValues' => $categoryBreakdown->pluck('total'),
        ]);
    }
}
