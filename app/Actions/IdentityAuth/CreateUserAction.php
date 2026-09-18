<?php

namespace App\Actions;

use App\Concerns\WritesAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Machec\Contracts\Enums\RoleName;

class CreateUserAction
{
    use WritesAuditLog;

    /**
     * Create an admin-managed user account and assign it a role (D97).
     */
    public function handle(Request $request, User $admin, string $name, string $email, string $password, RoleName $role): User
    {
        // D97: re-checked here, never trusted from hidden UI alone.
        abort_unless($admin->hasRole(RoleName::CustomerAdmin), 403);

        // D27-style split: structural validation already ran in UserForm;
        // the DB-touching uniqueness check stays here, same as RegisterAction.
        Validator::make(
            ['email' => $email],
            ['email' => Rule::unique(User::class)],
        )->validate();

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $user->assignRole($role);

        $this->recordAuditLog($request, $admin, 'user.created', $user);

        return $user;
    }
}
