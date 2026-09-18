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
        /** @var User $user */
        $user = Auth::user();

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $this->recordAuditLog($request, $user, 'logout');
    }
}
