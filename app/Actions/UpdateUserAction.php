<?php

namespace App\Actions;

use App\Concerns\WritesAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Machec\Contracts\Enums\RoleName;

class UpdateUserAction
{
    use WritesAuditLog;

    /**
     * Update an admin-managed user's name/email/role (D97).
     *
     * Self-edit guardrail: when the acting admin edits their own account,
     * any submitted email change is dropped server-side, never trusted
     * from a disabled form field alone — an admin can never mid-session
     * invalidate their own Fortify session identity this way.
     */
    public function handle(Request $request, User $admin, User $target, string $name, string $email, RoleName $role): User
    {
        // D97: re-checked here, never trusted from hidden UI alone.
        abort_unless($admin->hasRole(RoleName::CustomerAdmin), 403);

        $isSelf = $target->id === $admin->id;

        $target->name = $name;

        if (! $isSelf) {
            // Validate the email uniqueness only if it's actually being changed.
            if ($email !== $target->email) {
                Validator::make(
                    ['email' => $email],
                    ['email' => Rule::unique(User::class)->ignore($target->id)],
                )->validate();
            }

            $target->email = $email;
        }

        $target->save();

        $target->syncRoles([$role]);

        $this->recordAuditLog($request, $admin, 'user.updated', $target);

        return $target;
    }
}
