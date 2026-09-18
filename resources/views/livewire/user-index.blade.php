<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:heading size="lg">{{ __('Users') }}</flux:heading>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Roles') }}</flux:table.column>
            <flux:table.column>{{ __('Addresses') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @foreach ($user->roles as $role)
                            <flux:badge size="sm">{{ $role->name }}</flux:badge>
                        @endforeach
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->addresses_count }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button :href="route('users.show', $user)" size="sm" wire:navigate>
                            {{ __('View') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    {{ $this->users->links() }}
</div>
