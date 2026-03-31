<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Donation;
use App\Models\DonationCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function donation(Request $request)
    {
        $donations = $this->buildQuery($request)->latest('donated_at')->get();
        $approvedTotal = (int) $donations->where('verification_status', 'approved')->sum('amount');

        $categoryTotals = $donations->groupBy(fn ($item) => $item->category?->name ?? 'Tanpa Kategori')
            ->map(fn ($items) => $items->sum('amount'));

        return view('reports.donations', [
            'donations' => $donations,
            'summary' => [
                'total_masuk' => $approvedTotal,
                'total_terverifikasi' => $approvedTotal,
                'total_pending' => (int) $donations->where('verification_status', 'pending')->sum('amount'),
                'total_ditolak' => (int) $donations->where('verification_status', 'rejected')->sum('amount'),
            ],
            'categories' => DonationCategory::where('is_active', true)->orderBy('name')->get(),
            'activities' => Activity::where('is_active', true)->orderBy('title')->get(),
            'categoryLabels' => $categoryTotals->keys(),
            'categoryValues' => $categoryTotals->values(),
        ]);
    }

    public function donationPdf(Request $request)
    {
        $donations = $this->buildQuery($request)->latest('donated_at')->get();
        $approvedTotal = (int) $donations->where('verification_status', 'approved')->sum('amount');

        $pdf = Pdf::loadView('reports.pdf.donations-official', [
            'donations' => $donations,
            'summary' => [
                'total_masuk' => $approvedTotal,
                'total_terverifikasi' => $approvedTotal,
                'total_pending' => (int) $donations->where('verification_status', 'pending')->sum('amount'),
            ],
            'printedAt' => now(),
            'period' => [
                'start' => $request->input('start_date'),
                'end' => $request->input('end_date'),
            ],
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-donasi-resmi.pdf');
    }

    public function donationExcel(Request $request)
    {
        $donations = $this->buildQuery($request)->latest('donated_at')->get();
        $approvedTotal = (int) $donations->where('verification_status', 'approved')->sum('amount');

        $lines = [];
        $lines[] = [
            'Tanggal', 'Kode Donasi', 'Nama Donatur', 'Kategori', 'Kegiatan', 'Metode', 'Nominal', 'Status Pembayaran', 'Status Verifikasi', 'Kwitansi',
        ];

        foreach ($donations as $donation) {
            $lines[] = [
                optional($donation->donated_at)->format('d-m-Y H:i'),
                'DON-' . str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT),
                $donation->donor_name,
                $donation->category?->name ?? '-',
                $donation->activity?->title ?? '-',
                strtoupper($donation->payment_method),
                $donation->amount,
                strtoupper($donation->payment_status),
                strtoupper($donation->verification_status),
                $donation->receipt_number ?? '-',
            ];
        }

        $lines[] = ['', '', '', '', '', 'TOTAL (APPROVED)', $approvedTotal, '', '', ''];

        $csv = collect($lines)->map(function (array $row): string {
            return '"' . collect($row)->map(function ($value): string {
                return str_replace('"', '""', (string) $value);
            })->implode('","') . '"';
        })->implode("\r\n");

        return response($csv)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="laporan-donasi.xls"');
    }

    public function donationPrint(Request $request)
    {
        $donations = $this->buildQuery($request)->latest('donated_at')->get();
        $approvedTotal = (int) $donations->where('verification_status', 'approved')->sum('amount');

        return view('reports.print.donations', [
            'donations' => $donations,
            'summary' => [
                'total_masuk' => $approvedTotal,
                'total_terverifikasi' => $approvedTotal,
                'total_pending' => (int) $donations->where('verification_status', 'pending')->sum('amount'),
            ],
            'printedAt' => now(),
            'period' => [
                'start' => $request->input('start_date'),
                'end' => $request->input('end_date'),
            ],
        ]);
    }

    private function buildQuery(Request $request): Builder
    {
        $query = Donation::query()->with(['category', 'activity']);

        if ($request->filled('start_date')) {
            $query->whereDate('donated_at', '>=', $request->string('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('donated_at', '<=', $request->string('end_date'));
        }
        if ($request->filled('category_id')) {
            $query->where('donation_category_id', $request->integer('category_id'));
        }
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->integer('activity_id'));
        }

        return $query;
    }
}
