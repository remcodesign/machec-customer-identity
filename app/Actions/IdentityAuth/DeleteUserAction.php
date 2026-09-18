<?php

namespace App\Actions;

use App\Concerns\WritesAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Machec\Contracts\Enums\RoleName;

class DeleteUserAction
{
    use WritesAuditLog;

    /**
     * Delete an admin-managed user account (D97).
     *
     * Rejects — no-op plus an inline error, never a raw 500 — deleting the
     * acting admin's own account, or any account holding the
     * `CustomerAdmin` role, so a `customer_admin` can never delete every
     * remaining admin-capable account and lock the back office out.
     */
    public function handle(Request $request, User $admin, User $target): void
    {
        // D97: re-checked here, never trusted from hidden UI alone.
        abort_unless($admin->hasRole(RoleName::CustomerAdmin), 403);

        if ($target->id === $admin->id) {
            throw ValidationException::withMessages(['delete' => 'You cannot delete your own account.']);
        }

        if ($target->hasRole(RoleName::CustomerAdmin)) {
            throw ValidationException::withMessages(['delete' => 'You cannot delete another customer_admin account.']);
        }

        DB::transaction(function () use ($request, $admin, $target): void {
            $this->recordAuditLog($request, $admin, 'user.deleted', $target);

            $target->delete();
        });
    }
}
