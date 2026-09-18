<?php

namespace App\Actions\IdentityAuth;

use App\Concerns\WritesAuditLog;
use App\Data\Requests\RegisterData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Machec\Contracts\Enums\RoleName;

class RegisterAction
{
    use WritesAuditLog;

    /**
     * Register a new customer, assign the customer role, and issue the same
     * stateful session `LoginAction` does — no separate sign-in step (D79).
     */
    public function handle(Request $request, RegisterData $data): User
    {
        // D27: no exists:/unique: on RegisterData itself, even though
        // spatie/laravel-data supports #[Unique] directly — DB-touching
        // checks stay here, after RegisterData has already resolved, so
        // the Data class itself never needs a live database to construct.
        Validator::make(
            ['email' => $data->email],
            ['email' => Rule::unique(User::class)],
        )->validate();

        $user = User::create($data->all());

        $user->assignRole(RoleName::Customer);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        $this->recordAuditLog($request, $user, 'registered');

        return $user;
    }
}
