<?php

namespace App\Concerns;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

trait WritesAuditLog
{
    private function recordAuditLog(Request $request, User $user, string $action): void
    {
        AuditLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);
    }
}
