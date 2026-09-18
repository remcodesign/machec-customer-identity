<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <x-machec::stat-tile :label="__('Total users')" :value="$this->totalUsers" />
        <x-machec::stat-tile :label="__('New registrations (7 days)')" :value="$this->newRegistrations" />
        <x-machec::stat-tile :label="__('Total addresses')" :value="$this->totalAddresses" />
    </div>
    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
        <flux:heading size="lg">{{ __('Recent activity') }}</flux:heading>

        <flux:table class="mt-4">
            <flux:table.columns>
                <flux:table.column>{{ __('Action') }}</flux:table.column>
                <flux:table.column>{{ __('User') }}</flux:table.column>
                <flux:table.column>{{ __('Subject') }}</flux:table.column>
                <flux:table.column>{{ __('When') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->recentAuditLog as $entry)
                    <flux:table.row :key="$entry->id">
                        <flux:table.cell>{{ $entry->action }}</flux:table.cell>
                        <flux:table.cell>{{ $entry->user?->name ?? __('System') }}</flux:table.cell>
                        <flux:table.cell>
                            {{ class_basename($entry->subject_type) }} #{{ $entry->subject_id }}</flux:table.cell>
                        <flux:table.cell>{{ $entry->created_at?->diffForHumans() }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4">{{ __('No activity yet.') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
