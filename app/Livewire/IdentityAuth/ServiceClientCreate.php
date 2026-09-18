<?php

namespace App\Livewire\IdentityAuth;

use App\Actions\IdentityAuth\IssueServiceTokenAction;
use App\Enums\ServiceAbility;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Issue service token')]
class ServiceClientCreate extends Component
{
    public string $label = '';

    public string $ability = '';

    /**
     * Set only once, right after minting (D103) — never re-populated, and
     * gone the moment the browser reloads this page.
     */
    public ?string $plaintextToken = null;

    public function save(IssueServiceTokenAction $action): void
    {
        $validated = $this->validate([
            'label' => ['required', 'string', 'max:255'],
            'ability' => ['required', 'string', 'in:'.implode(',', array_map(
                fn (ServiceAbility $case): string => $case->value,
                ServiceAbility::cases(),
            ))],
        ]);

        /** @var User $admin */
        $admin = Auth::user();

        $result = $action->handle(
            request(),
            $admin,
            $validated['label'],
            ServiceAbility::from($validated['ability']),
        );

        $this->plaintextToken = $result['plaintextToken'];
    }

    public function dismissSecret(): void
    {
        $this->plaintextToken = null;

        $this->redirect(route('service-clients.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.service-client-create');
    }
}
