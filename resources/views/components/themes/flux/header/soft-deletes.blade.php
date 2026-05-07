<div wire:key="soft-deletes-dropdown-{{ $tableName }}">
    @if (data_get($setUp, 'header.softDeletes'))
        <flux:dropdown>
            <flux:button
                icon:trailing="chevron-down"
                class="{{ theme('header.export.button') }}"
            >
                <x-livewire-powergrid::icons.trash class="h-5 w-5" />
            </flux:button>

            <flux:menu>
                <flux:menu.item wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: ''})">
                    @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
                </flux:menu.item>
                <flux:menu.item wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'withTrashed'})">
                    @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
                </flux:menu.item>
                <flux:menu.item wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'onlyTrashed'})">
                    @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    @endif
</div>
