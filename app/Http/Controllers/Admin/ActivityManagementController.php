<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityManagementController extends Controller
{
    public function index(Request $request)
    {
        $isSuperadmin = $request->user()?->hasRole('superadmin') ?? false;
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
        $tab = (string) $request->query('tab', 'active');
        if (! in_array($tab, ['active', 'deleted'], true)) {
            $tab = 'active';
        }
        if ($tab === 'deleted' && ! $isSuperadmin) {
            $tab = 'active';
        }

        return view('admin.activities', [
            'tab' => $tab,
            'canViewDeleted' => $isSuperadmin,
            'perPage' => $perPage,
            'activities' => Activity::query()
                ->latest('start_at')
                ->paginate($perPage, ['*'], 'active_page')
                ->appends($request->query()),
            'deletedActivities' => $isSuperadmin
                ? Activity::onlyTrashed()
                    ->latest('deleted_at')
                    ->paginate($perPage, ['*'], 'deleted_page')
                    ->appends($request->query())
                : null,
        ]);
    }

    public function store(Request $request, AuditLogService $auditLogService)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'quota' => ['required', 'integer', 'min:1'],
        ]);

        $activity = Activity::create([
            ...$data,
            'slug' => Str::slug($data['title']) . '-' . Str::lower(Str::random(5)),
            'created_by' => $request->user()->id,
            'is_active' => true,
        ]);

        $auditLogService->record($request, 'create', 'Buat kegiatan: ' . $activity->title, 'activities', $activity->id);

        return back()->with('status', 'Kegiatan berhasil dibuat.');
    }

    public function update(Request $request, Activity $activity, AuditLogService $auditLogService)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'quota' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        $activity->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'] ?? null,
            'quota' => $data['quota'],
            'is_active' => (bool) $data['is_active'],
        ]);

        $auditLogService->record($request, 'update', 'Update kegiatan: ' . $activity->title, 'activities', $activity->id);

        return back()->with('status', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Request $request, Activity $activity, AuditLogService $auditLogService)
    {
        $title = $activity->title;
        $activityId = $activity->id;
        $activity->delete();

        $auditLogService->record($request, 'delete', 'Soft delete kegiatan: ' . $title, 'activities', $activityId);

        return back()->with('status', 'Kegiatan berhasil dihapus (soft delete).');
    }

    public function restore(Request $request, int $activityId, AuditLogService $auditLogService)
    {
        $activity = Activity::onlyTrashed()->findOrFail($activityId);
        $activity->restore();

        $auditLogService->record($request, 'restore', 'Restore kegiatan: ' . $activity->title, 'activities', $activity->id);

        return back()->with('status', 'Kegiatan berhasil direstore.');
    }

    public function forceDelete(Request $request, int $activityId, AuditLogService $auditLogService)
    {
        $activity = Activity::onlyTrashed()->findOrFail($activityId);
        $title = $activity->title;
        $id = $activity->id;
        $activity->forceDelete();

        $auditLogService->record($request, 'force_delete', 'Hapus permanen kegiatan: ' . $title, 'activities', $id);

        return back()->with('status', 'Kegiatan berhasil dihapus permanen.');
    }
}
