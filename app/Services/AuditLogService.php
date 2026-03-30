<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogService
{
    public function record(Request $request, string $action, string $description, ?string $targetType = null, ?int $targetId = null): void
    {
        ActivityLog::create([
            'user_id' => optional($request->user())->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 65535),
        ]);
    }
}
