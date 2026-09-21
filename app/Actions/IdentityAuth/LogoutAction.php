<?php

namespace App\Actions\IdentityAuth;

use App\Concerns\WritesAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutAction
{
    use WritesAuditLog;

    /**
     * Invalidate the current stateful session.
     */
    public function handle(Request $request): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // A call with no authenticated user (already logged out, or a
        // session the store never actually recognized) has no actor to
        // attribute the log to — skip it rather than crash.
        if ($user !== null) {
            $this->recordAuditLog($request, $user, 'logout');
        }
    }
}
