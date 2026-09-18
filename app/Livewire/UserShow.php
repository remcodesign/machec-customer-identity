<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('User')]
class UserShow extends Component
{
    public User $user;

    public ?int $editingAddressId = null;

    public bool $showAddressForm = false;

    public function mount(User $user): void
    {
        $this->user = $user;
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
