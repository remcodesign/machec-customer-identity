<div>
    <flux:heading size="lg"> {{ $form->address ? __('Edit address') : __('Add address') }} </flux:heading>

    <form wire:submit="save" class="mt-4 flex flex-col gap-4">
        <flux:field>
            <flux:label>{{ __('Label') }}</flux:label>
            <flux:input wire:model="form.label" />
            <flux:error name="form.label" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Address line 1') }}</flux:label>
            <flux:input wire:model="form.line1" />
            <flux:error name="form.line1" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Address line 2') }}</flux:label>
            <flux:input wire:model="form.line2" />
            <flux:error name="form.line2" />
        </flux:field>

        <div class="grid grid-cols-2 gap-4">
            <flux:field>
                <flux:label>{{ __('City') }}</flux:label>
                <flux:input wire:model="form.city" />
                <flux:error name="form.city" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Postal code') }}</flux:label>
                <flux:input wire:model="form.postal_code" />
                <flux:error name="form.postal_code" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>{{ __('Country code') }}</flux:label>
            <flux:input wire:model="form.country_code" maxlength="2" placeholder="NL" />
            <flux:error name="form.country_code" />
        </flux:field>

        <flux:field variant="inline">
            <flux:checkbox wire:model="form.is_default_shipping" />
            <flux:label>{{ __('Default shipping address') }}</flux:label>
        </flux:field>

        <flux:button type="submit" variant="primary" class="cursor-pointer">{{ __('Save') }}</flux:button>
    </form>
</div>
