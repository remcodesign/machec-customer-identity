<?php

namespace App\Actions\IdentityAuth;

use App\Concerns\WritesAuditLog;
use App\Models\ServiceClient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Machec\Contracts\Enums\RoleName;

class DeleteServiceClientAction
{
    use WritesAuditLog;

    /**
     * Delete a `ServiceClient` and every token it owns in one go (D101) —
     * a soft delete on the client, a hard delete on its tokens, so
     * Sanctum's own re-check on every request makes revocation immediate.
     * Writes its own `usr_audit_log` row (D63) — same admin-CRUD tier as
     * `DeleteUserAction`.
     */
    public function handle(Request $request, User $admin, ServiceClient $client): void
    {
        // D101: re-checked here, never trusted from hidden UI alone.
        abort_unless($admin->hasRole(RoleName::CustomerAdmin), 403);

        DB::transaction(function () use ($request, $admin, $client): void {
            $this->recordAuditLog($request, $admin, 'service_client.deleted', $client);

            $client->tokens()->delete();

            $client->delete();
        });
    }
}
