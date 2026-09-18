<?php

namespace App\Actions\IdentityAuth;

use App\Concerns\WritesAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Machec\Contracts\Enums\RoleName;

class RevokeServiceTokenAction
{
    use WritesAuditLog;

    /**
     * Revoke a single Sanctum token (D101) — a hard delete, instantly
     * effective since Sanctum re-checks this table on every request.
     * Writes its own `usr_audit_log` row (D63) — same admin-CRUD tier as
     * `DeleteServiceClientAction`.
     */
    public function handle(Request $request, User $admin, PersonalAccessToken $token): void
    {
        // D101: re-checked here, never trusted from hidden UI alone.
        abort_unless($admin->hasRole(RoleName::CustomerAdmin), 403);

        // Written before the delete — the row (and its id) is gone after.
        $this->recordAuditLog($request, $admin, 'service_client.token_revoked', $token);

        $token->delete();
    }
}
