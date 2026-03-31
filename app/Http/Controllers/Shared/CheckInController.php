<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\AttendanceLog;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function index(Request $request)
    {
        return view('shared.checkin', [
            'activities' => Activity::where('is_active', true)->orderBy('start_at')->get(),
        ]);
    }

    public function attendance(Request $request)
    {
        $selectedDate = (string) $request->input('log_date', now()->toDateString());
        $selectedActivityId = $request->filled('log_activity_id') ? $request->integer('log_activity_id') : null;

        $logsQuery = AttendanceLog::query()
            ->with([
                'registration:id,registration_code,participant_name',
                'activity:id,title,start_at',
                'handler:id,name',
            ])
            ->whereDate('checked_in_at', $selectedDate);

        if ($selectedActivityId) {
            $logsQuery->where('activity_id', $selectedActivityId);
        }

        $todayLogs = $logsQuery->latest('checked_in_at')->paginate(50)->withQueryString();

        return view('shared.attendance', [
            'todayLogs' => $todayLogs,
            'activities' => Activity::where('is_active', true)->orderBy('start_at')->get(),
            'selectedLogDate' => $selectedDate,
            'selectedLogActivityId' => $selectedActivityId,
        ]);
    }

    public function byCode(Request $request, AuditLogService $auditLogService)
    {
        $data = $request->validate([
            'registration_code' => ['required', 'string'],
        ]);

        $registration = ActivityRegistration::with('activity')
            ->where('registration_code', strtoupper($data['registration_code']))
            ->first();

        if (! $registration) {
            return back()->withErrors(['registration_code' => 'Kode pendaftaran tidak ditemukan.']);
        }

        $activity = $registration->activity;
        if (! $activity) {
            return back()->withErrors([
                'registration_code' => 'Data kegiatan untuk tiket ini tidak ditemukan.',
            ]);
        }

        if ($windowError = $this->checkinWindowError($activity, 'registration_code')) {
            return $windowError;
        }

        if ($registration->attendance_status === 'hadir') {
            return back()->withErrors(['registration_code' => 'Peserta sudah check-in sebelumnya.']);
        }

        $registration->update([
            'attendance_status' => 'hadir',
            'checked_in_at' => now(),
            'checkin_method' => 'kode',
        ]);

        AttendanceLog::create([
            'activity_registration_id' => $registration->id,
            'activity_id' => $registration->activity_id,
            'user_id' => $registration->user_id,
            'checked_in_at' => now(),
            'method' => 'kode',
            'handled_by' => $request->user()->id,
            'notes' => 'Check-in dengan kode pendaftaran',
        ]);

        $auditLogService->record($request, 'checkin', 'Check-in kode: ' . $registration->registration_code, 'activity_registrations', $registration->id);

        return back()->with('status', 'Check-in berhasil untuk ' . $registration->participant_name);
    }

    public function walkIn(Request $request, AuditLogService $auditLogService)
    {
        $data = $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
            'participant_name' => ['required', 'string', 'max:255'],
            'participant_age' => ['required', 'integer', 'min:0', 'max:120'],
            'participant_gender' => ['required', 'string', 'in:L,P'],
            'participant_address' => ['required', 'string', 'max:255'],
        ]);

        $activity = Activity::findOrFail($data['activity_id']);
        if ($windowError = $this->checkinWindowError($activity, 'activity_id')) {
            return $windowError;
        }

        $registration = ActivityRegistration::create([
            'activity_id' => $activity->id,
            'participant_name' => $data['participant_name'],
            'participant_age' => (int) $data['participant_age'],
            'participant_gender' => (string) $data['participant_gender'],
            'participant_address' => (string) $data['participant_address'],
            'registration_code' => strtoupper('WALK-' . now()->format('His') . '-' . random_int(100, 999)),
            'qr_payload' => 'walkin:' . uniqid(),
            'registration_type' => 'walkin',
            'attendance_status' => 'hadir',
            'registered_at' => now(),
            'checked_in_at' => now(),
            'checkin_method' => 'manual',
            'created_by' => $request->user()->id,
        ]);

        AttendanceLog::create([
            'activity_registration_id' => $registration->id,
            'activity_id' => $registration->activity_id,
            'user_id' => null,
            'checked_in_at' => now(),
            'method' => 'manual',
            'handled_by' => $request->user()->id,
            'notes' => 'Walk-in langsung hadir',
        ]);

        $activity->increment('registered_count');

        $auditLogService->record($request, 'walkin', 'Walk-in: ' . $registration->participant_name, 'activity_registrations', $registration->id);

        return back()->with('status', 'Walk-in berhasil dicatat dan langsung hadir.');
    }

    private function checkinWindowError(Activity $activity, string $errorField): ?RedirectResponse
    {
        $now = now();
        $startAt = $activity->start_at;
        $endAt = $activity->end_at ?: ($startAt ? $startAt->copy()->addHours(4) : null);

        if ($startAt && $now->lt($startAt)) {
            return back()->withErrors([
                $errorField => 'QR belum berlaku. Check-in dibuka mulai ' . $startAt->format('d-m-Y H:i') . '.',
            ]);
        }

        if ($endAt && $now->gt($endAt)) {
            return back()->withErrors([
                $errorField => 'QR sudah kedaluwarsa. Batas check-in sampai ' . $endAt->format('d-m-Y H:i') . '.',
            ]);
        }

        return null;
    }
}
