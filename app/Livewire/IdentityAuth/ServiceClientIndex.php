<?php

namespace App\Livewire\IdentityAuth;

use App\Actions\IdentityAuth\DeleteServiceClientAction;
use App\Actions\IdentityAuth\RevokeServiceTokenAction;
use App\Models\ServiceClient;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Service clients')]
class ServiceClientIndex extends Component
{
    /**
     * @return Collection<int, ServiceClient>
     */
    #[Computed]
    public function clients(): Collection
    {
        return ServiceClient::with('tokens')->orderBy('label')->get();
    }

    public function revokeToken(RevokeServiceTokenAction $action, int $tokenId): void
    {
        /** @var User $admin */
        $admin = Auth::user();

        $token = PersonalAccessToken::findOrFail($tokenId);

        $action->handle(request(), $admin, $token);

        unset($this->clients);
    }

    public function deleteClient(DeleteServiceClientAction $action, int $clientId): void
    {
        /** @var User $admin */
        $admin = Auth::user();

        $client = ServiceClient::findOrFail($clientId);

        $action->handle(request(), $admin, $client);

        unset($this->clients);
    }

    public function render(): View
    {
        return view('livewire.service-client-index');
    }
}
