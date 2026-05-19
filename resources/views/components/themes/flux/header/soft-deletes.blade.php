@if (data_get($setUp, 'header.softDeletes'))
    <div wire:key="soft-deletes-dropdown-{{ $tableName }}">
        <flux:dropdown>
            <flux:button variant="filled" class="!w-12 !h-10 !flex !items-center !justify-center">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </flux:button>

            <flux:menu class="dark:bg-zinc-900">
                <flux:menu.item
                    wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: ''})"
                >
                    @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
                </flux:menu.item>
                <flux:menu.item
                    wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'withTrashed'})"
                >
                    @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
                </flux:menu.item>
                <flux:menu.item
                    wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'onlyTrashed'})"
                >
                    @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
@endif
