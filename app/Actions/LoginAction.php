<?php

namespace App\Actions;

use App\Concerns\WritesAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAction
{
    use WritesAuditLog;

    /**
     * Authenticate a customer and issue a stateful Sanctum SPA session (D8).
     */
    public function handle(Request $request): User
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->attempt($credentials)) {
            abort(422, 'These credentials do not match our records.');
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        $this->recordAuditLog($request, $user, 'login');

        return $user;
    }
}
