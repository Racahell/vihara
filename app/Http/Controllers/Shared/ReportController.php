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
        $report = $this->buildDonationReport($request);

        return view('reports.donations', [
            'donations' => $report['donations'],
            'summary' => $report['summary'],
            'ledger' => $report['ledger'],
            'categories' => DonationCategory::where('is_active', true)->orderBy('name')->get(),
            'activities' => Activity::where('is_active', true)->orderBy('title')->get(),
            'categoryLabels' => $report['categoryTotals']->keys(),
            'categoryValues' => $report['categoryTotals']->values(),
            'methodLabels' => $report['methodTotals']->keys(),
            'methodValues' => $report['methodTotals']->values(),
        ]);
    }

    public function donationPdf(Request $request)
    {
        $report = $this->buildDonationReport($request);

        $pdf = Pdf::loadView('reports.pdf.donations-official', [
            'donations' => $report['donations'],
            'summary' => $report['summary'],
            'ledger' => $report['ledger'],
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
        $report = $this->buildDonationReport($request);
        $donations = $report['donations'];
        $summary = $report['summary'];
        $ledger = $report['ledger'];

        $lines = [];
        $lines[] = ['LAPORAN PENERIMAAN DAN PENGGUNAAN DANA DONASI'];
        $lines[] = ['Periode', (string) ($request->input('start_date') ?: '-') . ' s/d ' . (string) ($request->input('end_date') ?: '-')];
        $lines[] = [];
        $lines[] = ['RINGKASAN'];
        $lines[] = ['Saldo Awal', $ledger['saldo_awal']];
        $lines[] = ['Total Penerimaan Donasi (Approved)', $ledger['total_penerimaan']];
        $lines[] = ['Total Penyaluran Dana', $ledger['total_penyaluran']];
        $lines[] = ['Total Biaya Operasional', $ledger['total_operasional']];
        $lines[] = ['Surplus / (Defisit)', $ledger['surplus_defisit']];
        $lines[] = ['Saldo Akhir', $ledger['saldo_akhir']];
        $lines[] = ['Pending', $summary['total_pending']];
        $lines[] = ['Ditolak', $summary['total_ditolak']];
        $lines[] = [];
        $lines[] = ['DETAIL PENERIMAAN DONASI'];
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

        $lines[] = ['', '', '', '', '', 'TOTAL (APPROVED)', $ledger['total_penerimaan'], '', '', ''];
        $lines[] = [];
        $lines[] = ['DETAIL PENYALURAN DANA (INPUT MANUAL PERIODE)', '', '', '', '', '', $ledger['total_penyaluran']];
        $lines[] = ['DETAIL BIAYA OPERASIONAL (INPUT MANUAL PERIODE)', '', '', '', '', '', $ledger['total_operasional']];

        $delimiter = ';';
        $csvRows = collect($lines)->map(function (array $row) use ($delimiter): string {
            return collect($row)->map(function ($value) use ($delimiter): string {
                $cell = str_replace(["\r\n", "\r", "\n"], ' ', (string) $value);
                $cell = str_replace('"', '""', $cell);

                return '"' . $cell . '"';
            })->implode($delimiter);
        });

        // "sep=;" membantu Excel (regional Indonesia) membaca delimiter dengan benar.
        $csv = "\xEF\xBB\xBF" . "sep={$delimiter}\r\n" . $csvRows->implode("\r\n");

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="laporan-donasi.csv"');
    }

    public function donationPrint(Request $request)
    {
        $report = $this->buildDonationReport($request);

        return view('reports.print.donations', [
            'donations' => $report['donations'],
            'summary' => $report['summary'],
            'ledger' => $report['ledger'],
            'printedAt' => now(),
            'period' => [
                'start' => $request->input('start_date'),
                'end' => $request->input('end_date'),
            ],
        ]);
    }

    private function buildDonationReport(Request $request): array
    {
        $donations = $this->buildQuery($request)->latest('donated_at')->get();
        $approvedDonations = $donations->where('verification_status', 'approved');
        $approvedTotal = (int) $approvedDonations->sum('amount');

        $saldoAwal = $this->moneyInput($request, 'saldo_awal');
        $totalPenyaluran = $this->moneyInput($request, 'total_penyaluran');
        $totalOperasional = $this->moneyInput($request, 'total_operasional');

        $surplusDefisit = $approvedTotal - ($totalPenyaluran + $totalOperasional);
        $saldoAkhir = $saldoAwal + $surplusDefisit;

        $categoryTotals = $approvedDonations
            ->groupBy(fn ($item) => $item->category?->name ?? 'Tanpa Kategori')
            ->map(fn ($items) => (int) $items->sum('amount'))
            ->sortKeys();

        $methodTotals = $approvedDonations
            ->groupBy(fn ($item) => strtoupper((string) data_get($item->payment_payload, 'channel', $item->payment_method)))
            ->map(fn ($items) => (int) $items->sum('amount'));

        return [
            'donations' => $donations,
            'summary' => [
                'total_masuk' => $approvedTotal,
                'total_terverifikasi' => $approvedTotal,
                'total_pending' => (int) $donations->where('verification_status', 'pending')->sum('amount'),
                'total_ditolak' => (int) $donations->where('verification_status', 'rejected')->sum('amount'),
            ],
            'ledger' => [
                'saldo_awal' => $saldoAwal,
                'total_penerimaan' => $approvedTotal,
                'total_penyaluran' => $totalPenyaluran,
                'total_operasional' => $totalOperasional,
                'surplus_defisit' => $surplusDefisit,
                'saldo_akhir' => $saldoAkhir,
            ],
            'categoryTotals' => $categoryTotals,
            'methodTotals' => $methodTotals,
        ];
    }

    private function moneyInput(Request $request, string $key): int
    {
        $raw = (string) $request->input($key, '0');
        $numeric = preg_replace('/[^\d]/', '', $raw);

        return (int) ($numeric ?: 0);
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
