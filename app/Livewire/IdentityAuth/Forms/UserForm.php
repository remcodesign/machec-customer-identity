<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Livewire\Form;
use Machec\Contracts\Enums\RoleName;

class UserForm extends Form
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = '';

    /**
     * This app's own three assignable roles (D97) — never the other two
     * apps' `RoleName` cases, even though the enum itself is shared (D12).
     *
     * @return array<int, RoleName>
     */
    public static function allowedRoles(): array
    {
        return [RoleName::Customer, RoleName::CustomerAdmin, RoleName::DataAdmin];
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->role = $user?->getRoleNames()->first() ?? '';
    }

    /**
     * Overridden instead of using #[Validate] attributes: the `role` list is
     * built from allowedRoles() and `password` is only required when creating.
     *
     * @return array<string, array<int, string|Password>>
     */
    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['required', 'string', 'in:'.implode(',', array_map(
                fn (RoleName $role): string => $role->value,
                self::allowedRoles(),
            ))],
        ];

        if (! $this->user instanceof User) {
            // Password is only required when creating a new user.
            $rules['password'] = ['required', 'string', Password::default(), 'confirmed'];
        }

        return $rules;
    }
}
