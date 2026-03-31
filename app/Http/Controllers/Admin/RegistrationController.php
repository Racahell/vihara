<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);
        $perPage = (int) $request->integer('per_page', 20);
        if (! in_array($perPage, [10, 20, 50, 100], true)) {
            $perPage = 20;
        }

        $registrations = $query->latest('registered_at')->paginate($perPage)->withQueryString();

        return view('admin.registrations', [
            'registrations' => $registrations,
            'activities' => Activity::query()->orderBy('title')->get(['id', 'title']),
            'perPage' => $perPage,
            'summary' => [
                'total' => (clone $query)->count(),
                'hadir' => (clone $query)->where('attendance_status', 'hadir')->count(),
                'belum' => (clone $query)->where('attendance_status', 'belum')->count(),
            ],
        ]);
    }

    public function excel(Request $request)
    {
        $registrations = $this->buildQuery($request)->latest('registered_at')->get();

        $lines = [];
        $lines[] = ['Kode', 'Peserta', 'No HP', 'Usia', 'JK', 'Kegiatan', 'Tipe', 'Status Hadir', 'Waktu Daftar'];

        foreach ($registrations as $reg) {
            $lines[] = [
                $reg->registration_code,
                $reg->participant_name,
                $reg->participant_phone ?? '-',
                $reg->participant_age ?? '-',
                strtoupper((string) $reg->participant_gender ?: '-'),
                $reg->activity->title ?? '-',
                strtoupper((string) $reg->registration_type),
                strtoupper((string) $reg->attendance_status),
                optional($reg->registered_at)->format('d-m-Y H:i'),
            ];
        }

        $csv = collect($lines)->map(function (array $row): string {
            return '"' . collect($row)->map(function ($value): string {
                return str_replace('"', '""', (string) $value);
            })->implode('","') . '"';
        })->implode("\r\n");

        return response($csv)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="pendaftaran-kegiatan.xls"');
    }

    public function pdf(Request $request)
    {
        $registrations = $this->buildQuery($request)->latest('registered_at')->get();

        $pdf = Pdf::loadView('admin.registrations-pdf', [
            'registrations' => $registrations,
            'printedAt' => now(),
            'period' => [
                'start' => $request->input('start_date'),
                'end' => $request->input('end_date'),
            ],
            'activityTitle' => $this->selectedActivityTitle($request),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-pendaftaran-kegiatan.pdf');
    }

    public function print(Request $request)
    {
        $registrations = $this->buildQuery($request)->latest('registered_at')->get();

        return view('admin.registrations-print', [
            'registrations' => $registrations,
            'printedAt' => now(),
            'period' => [
                'start' => $request->input('start_date'),
                'end' => $request->input('end_date'),
            ],
            'activityTitle' => $this->selectedActivityTitle($request),
        ]);
    }

    private function buildQuery(Request $request): Builder
    {
        $query = ActivityRegistration::query()->with('activity');

        if ($request->filled('start_date')) {
            $query->whereDate('registered_at', '>=', $request->string('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('registered_at', '<=', $request->string('end_date'));
        }

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->integer('activity_id'));
        }

        return $query;
    }

    private function selectedActivityTitle(Request $request): string
    {
        if (! $request->filled('activity_id')) {
            return 'Semua kegiatan';
        }

        return (string) (Activity::query()->whereKey($request->integer('activity_id'))->value('title') ?: 'Semua kegiatan');
    }
}
