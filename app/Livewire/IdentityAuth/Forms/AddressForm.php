<?php

namespace App\Livewire\IdentityAuth\Forms;

use App\Concerns\WritesAuditLog;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AddressForm extends Form
{
    use WritesAuditLog;

    public ?Address $address = null;

    #[Validate('required|string|max:255')]
    public string $label = '';

    #[Validate('required|string|max:255')]
    public string $line1 = '';

    #[Validate('nullable|string|max:255')]
    public string $line2 = '';

    #[Validate('required|string|max:255')]
    public string $city = '';

    #[Validate('required|string|max:20')]
    public string $postal_code = '';

    #[Validate('required|string|size:2')]
    public string $country_code = '';

    #[Validate('boolean')]
    public bool $is_default_shipping = false;

    public function setAddress(?Address $address): void
    {
        $this->address = $address;

        $this->label = $address->label ?? '';
        $this->line1 = $address->line1 ?? '';
        $this->line2 = (string) ($address->line2 ?? '');
        $this->city = $address->city ?? '';
        $this->postal_code = $address->postal_code ?? '';
        $this->country_code = $address->country_code ?? '';
        $this->is_default_shipping = $address->is_default_shipping ?? false;
    }

    public function save(User $user): void
    {
        $validated = $this->validate();
        // Convert empty line2 to null for database storage.
        $validated['line2'] = $validated['line2'] ?: null;

        // Determine if we are creating a new address or updating an existing one.
        $isCreating = ! $this->address instanceof Address;

        if ($this->address instanceof Address) {
            $this->address->update($validated);
        } else {
            $this->address = $user->addresses()->create($validated);
        }

        /** @var User $admin */
        $admin = Auth::user();

        $this->recordAuditLog(
            request(),
            $admin,
            $isCreating ? 'address.created' : 'address.updated',
            $this->address,
        );

        $this->reset();
    }
}
