<?php

namespace App\Livewire;

use App\Livewire\Forms\AddressForm as AddressFormData;
use App\Models\Address;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AddressForm extends Component
{
    public User $user;

    public AddressFormData $form;

    public function mount(User $user, ?Address $address = null): void
    {
        $this->user = $user;
        $this->form->setAddress($address);
    }

    public function save(): void
    {
        $this->form->save($this->user);

        $this->dispatch('address-saved');
    }

    public function render(): View
    {
        return view('livewire.address-form');
    }
}
