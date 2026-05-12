@if (data_get($setUp, 'header.softDeletes'))
    <div class="dropdown" :class="{ 'dropdown-open': open }" x-data="{ open: false }" x-on:keydown.esc="open = false" x-on:click.outside="open = false" wire:key="soft-deletes-dropdown-{{ $tableName }}">
        <div tabindex="0" role="button" class="{{ theme('header.layout.actions') }}" x-on:click="open = !open">
            <x-livewire-powergrid::icons.trash class="w-4 h-4" />
        </div>
        <ul tabindex="0" class="dropdown-content z-[50] menu p-2 shadow bg-base-100 rounded-box w-52 mt-2">
            <li>
                <a wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: ''})">
                    @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
                </a>
            </li>
            <li>
                <a wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'withTrashed'})">
                    @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
                </a>
            </li>
            <li>
                <a wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'onlyTrashed'})">
                    @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
                </a>
            </li>
        </ul>
    </div>
@endif
