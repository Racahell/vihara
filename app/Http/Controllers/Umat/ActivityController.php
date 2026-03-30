<?php

namespace App\Http\Controllers\Umat;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\FavoriteActivity;
use App\Services\AuditLogService;
use App\Support\RegistrationCode;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('umat.activities', [
            'activities' => Activity::where('is_active', true)->orderBy('start_at')->paginate(12),
            'favoriteIds' => FavoriteActivity::where('user_id', $user->id)->pluck('activity_id')->all(),
        ]);
    }

    public function show(Activity $activity)
    {
        return view('umat.activity-detail', ['activity' => $activity]);
    }

    public function register(Request $request, Activity $activity, AuditLogService $auditLogService)
    {
        $user = $request->user();

        if ($activity->registered_count >= $activity->quota) {
            return back()->withErrors(['quota' => 'Kuota kegiatan sudah penuh.']);
        }

        if (ActivityRegistration::where('activity_id', $activity->id)->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['duplikat' => 'Anda sudah terdaftar pada kegiatan ini.']);
        }

        $code = RegistrationCode::make('REG');

        $registration = ActivityRegistration::create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'participant_name' => $user->name,
            'participant_phone' => $user->phone,
            'registration_code' => $code,
            'qr_payload' => 'reg:' . $code,
            'registration_type' => 'regular',
            'attendance_status' => 'belum',
            'registered_at' => now(),
            'created_by' => $user->id,
        ]);

        $activity->increment('registered_count');

        $auditLogService->record($request, 'register_activity', 'Daftar kegiatan: ' . $activity->title, 'activity_registrations', $registration->id);

        return redirect()->route('umat.my-history')->with('status', 'Pendaftaran berhasil. Kode tiket: ' . $code);
    }

    public function favorite(Request $request, Activity $activity)
    {
        FavoriteActivity::firstOrCreate([
            'user_id' => $request->user()->id,
            'activity_id' => $activity->id,
        ]);

        return back()->with('status', 'Kegiatan ditambahkan ke favorit.');
    }
}
