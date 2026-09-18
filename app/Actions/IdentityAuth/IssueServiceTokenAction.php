<?php

namespace App\Actions\IdentityAuth;

use App\Concerns\WritesAuditLog;
use App\Enums\ServiceAbility;
use App\Models\ServiceClient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Machec\Contracts\Enums\RoleName;

class IssueServiceTokenAction
{
    use WritesAuditLog;

    /**
     * Mint a new Sanctum M2M token for a (possibly new) `ServiceClient`
     * (D99/D101) — the one place the plaintext token exists in memory,
     * which is also where `token_prefix` (D102) is captured from it.
     *
     * `$admin` is null for the console front door (D99) — a scripted mint
     * is already gated by server/SSH access, the same human-in-the-loop
     * trust D99 always assumed for it; the role check only applies to the
     * Livewire front door, where an authenticated web admin is doing this,
     * and only that path writes a `usr_audit_log` row (D63) — minting a
     * cross-app credential is admin CRUD, the same tier D97's user-management
     * screens already audit (D101's own framing).
     *
     * @return array{client: ServiceClient, plaintextToken: string}
     */
    public function handle(Request $request, ?User $admin, string $label, ServiceAbility $ability): array
    {
        // D101: re-checked here, never trusted from hidden UI alone.
        if ($admin instanceof User) {
            abort_unless($admin->hasRole(RoleName::CustomerAdmin), 403);
        }

        $client = ServiceClient::firstOrCreate(['label' => $label]);

        $newToken = $client->createToken($label, [$ability->value]);

        $plaintextToken = $newToken->plainTextToken;

        $newToken->accessToken->token_prefix = Str::of($plaintextToken)->after('|')->substr(0, 8)->value();
        $newToken->accessToken->save();

        if ($admin instanceof User) {
            $this->recordAuditLog($request, $admin, 'service_client.token_issued', $newToken->accessToken);
        }

        return ['client' => $client, 'plaintextToken' => $plaintextToken];
    }
}
