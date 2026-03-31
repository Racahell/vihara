<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\AttendanceLog;
use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $canSeeIncomeReport = $user?->hasAnyRole(['superadmin', 'manager', 'owner', 'admin']) ?? false;
        $canSeeDonationWidgets = $user?->hasAnyRole(['superadmin', 'manager', 'owner', 'admin']) ?? false;
        $isPetugas = $user?->hasRole('petugas') ?? false;

        $stats = [
            'kegiatan_aktif' => Activity::where('is_active', true)->count(),
            'pendaftaran_total' => ActivityRegistration::count(),
            'hadir_hari_ini' => ActivityRegistration::whereDate('checked_in_at', now()->toDateString())->count(),
            'donasi_berhasil' => Donation::where('verification_status', 'approved')->sum('amount'),
            'umat_aktif' => User::query()
                ->where('is_active', true)
                ->whereHas('roles', fn ($q) => $q->where('slug', 'umat'))
                ->count(),
            'checkin_online_hari_ini' => AttendanceLog::query()
                ->whereDate('checked_in_at', now()->toDateString())
                ->where('method', 'kode')
                ->count(),
            'walkin_hari_ini' => AttendanceLog::query()
                ->whereDate('checked_in_at', now()->toDateString())
                ->where('method', 'manual')
                ->count(),
        ];

        $incomeReport = [
            'daily' => 0,
            'today' => 0,
            'yesterday' => 0,
        ];

        if ($canSeeIncomeReport) {
            $approvedDonations = Donation::query()
                ->where('verification_status', 'approved');

            $incomeReport['daily'] = (int) (clone $approvedDonations)
                ->where('donated_at', '>=', now()->subDay())
                ->sum('amount');
            $incomeReport['today'] = (int) (clone $approvedDonations)
                ->whereDate('donated_at', now()->toDateString())
                ->sum('amount');
            $incomeReport['yesterday'] = (int) (clone $approvedDonations)
                ->whereDate('donated_at', now()->subDay()->toDateString())
                ->sum('amount');
        }

        $monthlyDonations = collect();
        $categoryBreakdown = collect();
        $petugasDailyLabels = collect();
        $petugasDailyValues = collect();
        $petugasMethodLabels = collect();
        $petugasMethodValues = collect();
        if ($canSeeDonationWidgets) {
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
        }

        if ($isPetugas) {
            $period = CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay());
            $dailyBase = collect($period)->mapWithKeys(fn ($date) => [$date->format('d M') => 0]);

            $dailyRows = AttendanceLog::query()
                ->selectRaw("DATE(checked_in_at) as checkin_date, COUNT(*) as total")
                ->whereDate('checked_in_at', '>=', now()->subDays(6)->toDateString())
                ->groupBy('checkin_date')
                ->orderBy('checkin_date')
                ->get();

            foreach ($dailyRows as $row) {
                $key = \Carbon\Carbon::parse((string) $row->checkin_date)->format('d M');
                if ($dailyBase->has($key)) {
                    $dailyBase[$key] = (int) $row->total;
                }
            }

            $methodRows = AttendanceLog::query()
                ->selectRaw('method, COUNT(*) as total')
                ->whereDate('checked_in_at', now()->toDateString())
                ->groupBy('method')
                ->pluck('total', 'method');

            $petugasDailyLabels = $dailyBase->keys()->values();
            $petugasDailyValues = $dailyBase->values()->values();
            $petugasMethodLabels = collect(['Online (Kode)', 'Walk-In']);
            $petugasMethodValues = collect([
                (int) ($methodRows['kode'] ?? 0),
                (int) ($methodRows['manual'] ?? 0),
            ]);
        }

        return view('shared.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'monthlyDonationLabels' => $monthlyDonations->pluck('month_key'),
            'monthlyDonationValues' => $monthlyDonations->pluck('total'),
            'categoryLabels' => $categoryBreakdown->pluck('name'),
            'categoryValues' => $categoryBreakdown->pluck('total'),
            'canSeeIncomeReport' => $canSeeIncomeReport,
            'canSeeDonationWidgets' => $canSeeDonationWidgets,
            'isPetugas' => $isPetugas,
            'incomeReport' => $incomeReport,
            'petugasDailyLabels' => $petugasDailyLabels,
            'petugasDailyValues' => $petugasDailyValues,
            'petugasMethodLabels' => $petugasMethodLabels,
            'petugasMethodValues' => $petugasMethodValues,
        ]);
    }
}
