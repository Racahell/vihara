<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\AttendanceLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function index()
    {
        return view('shared.checkin', [
            'todayLogs' => AttendanceLog::latest()->take(50)->get(),
            'activities' => Activity::where('is_active', true)->orderBy('start_at')->get(),
        ]);
    }

    public function byCode(Request $request, AuditLogService $auditLogService)
    {
        $data = $request->validate([
            'registration_code' => ['required', 'string'],
        ]);

        $registration = ActivityRegistration::where('registration_code', strtoupper($data['registration_code']))->first();

        if (! $registration) {
            return back()->withErrors(['registration_code' => 'Kode pendaftaran tidak ditemukan.']);
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
            'participant_phone' => ['nullable', 'string', 'max:32'],
        ]);

        $activity = Activity::findOrFail($data['activity_id']);

        $registration = ActivityRegistration::create([
            'activity_id' => $activity->id,
            'participant_name' => $data['participant_name'],
            'participant_phone' => $data['participant_phone'] ?? null,
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
}
