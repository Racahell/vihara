<?php

namespace App\Http\Controllers\Umat;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\FavoriteActivity;
use App\Services\AuditLogService;
use App\Support\RegistrationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ActivityController extends Controller
{
    public function index()
    {
        return $this->renderActivities(false);
    }

    public function favorites()
    {
        return $this->renderActivities(true);
    }

    public function show(Activity $activity)
    {
        return view('umat.activity-detail', ['activity' => $activity]);
    }

    public function register(Request $request, Activity $activity, AuditLogService $auditLogService)
    {
        $user = $request->user();
        $data = $request->validate([
            'participants' => ['required', 'array', 'min:1', 'max:10'],
            'participants.*.name' => ['required', 'string', 'max:255'],
            'participants.*.age' => ['required', 'integer', 'min:0', 'max:120'],
            'participants.*.gender' => ['required', 'string', Rule::in(['L', 'P'])],
            'participants.*.address' => ['required', 'string', 'max:255'],
        ]);
        $participants = collect($data['participants'])->values();
        $requestedCount = $participants->count();

        if (($activity->registered_count + $requestedCount) > $activity->quota) {
            return back()->withErrors(['quota' => 'Kuota kegiatan sudah penuh.']);
        }

        $createdCount = 0;
        $codes = [];

        DB::transaction(function () use ($participants, $activity, $requestedCount, $user, &$createdCount, &$codes, $request, $auditLogService): void {
            $lockedActivity = Activity::query()->whereKey($activity->id)->lockForUpdate()->firstOrFail();
            if (($lockedActivity->registered_count + $requestedCount) > $lockedActivity->quota) {
                throw ValidationException::withMessages([
                    'quota' => ['Sisa kuota kegiatan tidak mencukupi untuk jumlah peserta yang didaftarkan.'],
                ]);
            }

            foreach ($participants as $participant) {
                $code = RegistrationCode::make('REG');
                $registration = ActivityRegistration::create([
                    'activity_id' => $lockedActivity->id,
                    'user_id' => $user->id,
                    'participant_name' => (string) $participant['name'],
                    'participant_phone' => $user->phone,
                    'participant_age' => (int) $participant['age'],
                    'participant_gender' => (string) $participant['gender'],
                    'participant_address' => (string) $participant['address'],
                    'registration_code' => $code,
                    'qr_payload' => 'reg:' . $code,
                    'registration_type' => 'regular',
                    'attendance_status' => 'belum',
                    'registered_at' => now(),
                    'created_by' => $user->id,
                ]);

                $createdCount++;
                $codes[] = $code;

                $auditLogService->record(
                    $request,
                    'register_activity',
                    'Daftar kegiatan: ' . $lockedActivity->title . ' - Peserta: ' . $registration->participant_name,
                    'activity_registrations',
                    $registration->id
                );
            }

            $lockedActivity->increment('registered_count', $createdCount);
        });

        return redirect()
            ->route('umat.my-history')
            ->with('status', 'Pendaftaran berhasil untuk ' . $createdCount . ' peserta. Kode: ' . implode(', ', $codes));
    }

    public function favorite(Request $request, Activity $activity)
    {
        $userId = (int) $request->user()->id;

        $existing = FavoriteActivity::query()
            ->where('user_id', $userId)
            ->where('activity_id', (int) $activity->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('status', 'Kegiatan dihapus dari favorit.');
        }

        FavoriteActivity::create([
            'user_id' => $userId,
            'activity_id' => $activity->id,
        ]);

        return back()->with('status', 'Kegiatan ditambahkan ke favorit.');
    }

    private function renderActivities(bool $favoritesOnly)
    {
        $user = auth()->user();
        $favoriteIds = FavoriteActivity::where('user_id', $user->id)->pluck('activity_id')->all();

        $query = Activity::where('is_active', true)->orderBy('start_at');
        if ($favoritesOnly) {
            $query->whereIn('id', $favoriteIds ?: [0]);
        }

        return view('umat.activities', [
            'activities' => $query->paginate(12),
            'favoriteIds' => $favoriteIds,
            'favoritesOnly' => $favoritesOnly,
        ]);
    }
}
