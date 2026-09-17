<?php

namespace App\Actions;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Concerns\WritesAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Machec\Contracts\Enums\RoleName;

class RegisterAction
{
    use PasswordValidationRules, ProfileValidationRules, WritesAuditLog;

    /**
     * Register a new customer, assign the customer role, and issue the same
     * stateful session `LoginAction` does — no separate sign-in step (D79).
     */
    public function handle(Request $request): User
    {
        $data = $request->validate([
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $user->assignRole(RoleName::Customer);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        $this->recordAuditLog($request, $user, 'registered');

        return $user;
    }
}
