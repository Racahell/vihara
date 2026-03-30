<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityManagementController extends Controller
{
    public function index()
    {
        return view('admin.activities', [
            'activities' => Activity::latest('start_at')->paginate(12),
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
}
