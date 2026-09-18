<?php

namespace App\Livewire;

use App\Actions\UpdateUserAction;
use App\Livewire\Forms\UserForm;
use App\Models\Address;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Machec\Contracts\Enums\RoleName;

#[Layout('layouts.app')]
#[Title('User')]
class UserShow extends Component
{
    public User $user;

    public ?int $editingAddressId = null;

    public bool $showAddressForm = false;

    public UserForm $form;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->form->setUser($user);
    }

    /**
     * customer_admin-only edit section (D97); data_admin keeps the
     * original read-only view.
     */
    #[Computed]
    public function canEdit(): bool
    {
        /** @var User $viewer */
        $viewer = Auth::user();

        return $viewer->hasRole(RoleName::CustomerAdmin);
    }

    /**
     * Self-edit guardrail (D97): an admin editing their own account can
     * never change their own email through this form.
     */
    #[Computed]
    public function editingSelf(): bool
    {
        /** @var User $viewer */
        $viewer = Auth::user();

        return $this->user->id === $viewer->id;
    }

    public function updateUser(UpdateUserAction $action): void
    {
        $validated = $this->form->validate();

        /** @var User $admin */
        $admin = Auth::user();

        $this->user = $action->handle(
            request(),
            $admin,
            $this->user,
            $validated['name'],
            $validated['email'],
            RoleName::from($validated['role']),
        );

        $this->form->setUser($this->user);
    }

    /**
     * @return Collection<int, Address>
     */
    #[Computed]
    public function addresses(): Collection
    {
        return $this->user->addresses()->orderByDesc('is_default_shipping')->get();
    }

    public function addAddress(): void
    {
        $this->editingAddressId = null;
        $this->showAddressForm = true;
    }

    public function editAddress(int $addressId): void
    {
        $this->editingAddressId = $addressId;
        $this->showAddressForm = true;
    }

    #[On('address-saved')]
    public function closeAddressForm(): void
    {
        $this->showAddressForm = false;
        $this->editingAddressId = null;
        unset($this->addresses);
    }

    public function render(): View
    {
        return view('livewire.user-show');
    }
}
