@if (data_get($setUp, 'header.softDeletes'))
    <div class="{{ theme('header.soft_deletes.container') }}">
        <div tabindex="0" role="button" class="{{ theme('header.soft_deletes.button') }}">
            <x-livewire-powergrid::icons.trash class="w-4 h-4" />
        </div>
        <ul tabindex="0" class="{{ theme('header.soft_deletes.menu') }}">
            <li class="{{ theme('header.soft_deletes.menu_item') }}">
                <a wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: ''})">
                    @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
                </a>
            </li>
            <li class="{{ theme('header.soft_deletes.menu_item') }}">
                <a wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'withTrashed'})">
                    @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
                </a>
            </li>
            <li class="{{ theme('header.soft_deletes.menu_item') }}">
                <a wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'onlyTrashed'})">
                    @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
                </a>
            </li>
        </ul>
    </div>
@endif
