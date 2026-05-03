@if (data_get($setUp, 'header.softDeletes'))
    <flux:dropdown>
        <flux:button
            icon:trailing="chevron-down"
            class="{{ theme('header.soft_deletes.button') }}"
        >
            <x-livewire-powergrid::icons.trash class="w-5 h-5" />
        </flux:button>

        <flux:menu class="w-48">
            <flux:menu.item x-on:click="$wire.dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: ''})">
                @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
            </flux:menu.item>
            <flux:menu.item x-on:click="$wire.dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'withTrashed'})">
                @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
            </flux:menu.item>
            <flux:menu.item x-on:click="$wire.dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'onlyTrashed'})">
                @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
@endif
