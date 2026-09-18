<?php

namespace App\Livewire;

use App\Actions\CreateUserAction;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Machec\Contracts\Enums\RoleName;

#[Layout('layouts.app')]
#[Title('Create user')]
class UserCreate extends Component
{
    public UserForm $form;

    public function save(CreateUserAction $action): void
    {
        $validated = $this->form->validate();

        /** @var User $admin */
        $admin = Auth::user();

        $user = $action->handle(
            request(),
            $admin,
            $validated['name'],
            $validated['email'],
            $validated['password'],
            RoleName::from($validated['role']),
        );

        $this->redirect(route('users.show', $user), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.user-create');
    }
}
