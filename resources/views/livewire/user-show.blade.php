<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="lg">{{ $user->name }}</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ $user->email }}</flux:text>
        </div>
        <div class="flex gap-1">
            @foreach ($user->roles as $role)
                <flux:badge size="sm">{{ $role->name }}</flux:badge>
            @endforeach
        </div>
    </div>

    <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
        <div class="flex items-center justify-between">
            <flux:heading size="md">{{ __('Addresses') }}</flux:heading>
            <flux:button size="sm" class="cursor-pointer" wire:click="addAddress">{{ __('Add address') }}</flux:button>
        </div>

        <flux:table class="mt-4">
            <flux:table.columns>
                <flux:table.column>{{ __('Label') }}</flux:table.column>
                <flux:table.column>{{ __('Address') }}</flux:table.column>
                <flux:table.column>{{ __('Default shipping') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->addresses as $address)
                    <flux:table.row :key="$address->id">
                        <flux:table.cell>{{ $address->label }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $address->line1 }}, {{ $address->city }} {{ $address->postal_code }}, {{ $address->country_code }}</flux:table.cell>
                        <flux:table.cell>{{ $address->is_default_shipping ? __('Yes') : __('No') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                size="sm"
                                class="cursor-pointer"
                                wire:click="editAddress({{ $address->id }})"
                            >{{ __('Edit') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4">{{ __('No addresses yet.') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:modal wire:model="showAddressForm" class="md:w-96">
        <livewire:identity-auth.address-form
            :user="$user"
            :address="$this->addresses->firstWhere('id', $editingAddressId)"
            :key="'address-form-'.($editingAddressId ?? 'new')"
        />
    </flux:modal>

    @if ($this->canEdit)
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:heading size="md">{{ __('Edit user') }}</flux:heading>

            <form wire:submit="updateUser" class="mt-4 max-w-lg space-y-6">
                <flux:field>
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model="form.name" />
                    <flux:error name="form.name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Email') }}</flux:label>
                    <flux:input type="email" wire:model="form.email" :disabled="$this->editingSelf" />
                    <flux:error name="form.email" />
                </flux:field>

                <flux:field>
                    <x-machec::role-select
                        wire:model="form.role"
                        :roles="\App\Livewire\IdentityAuth\Forms\UserForm::allowedRoles()"
                        :label="__('Role')"
                    />
                    <flux:error name="form.role" />
                </flux:field>

                <div class="flex justify-end">
                    <flux:button
                        type="submit"
                        variant="primary"
                        class="cursor-pointer"
                    >{{ __('Save changes') }}</flux:button>
                </div>
            </form>
        </div>
    @endif
</div>
