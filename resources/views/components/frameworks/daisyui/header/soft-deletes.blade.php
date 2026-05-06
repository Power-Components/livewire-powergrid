@if (data_get($setUp, 'header.softDeletes'))
    <div class="dropdown">
        <div tabindex="0" role="button" class="{{ theme('header.layout.actions') }}">
            <x-livewire-powergrid::icons.trash class="w-4 h-4" />
        </div>
        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52 mt-2">
            <li>
                <a wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: ''})">
                    @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
                </a>
            </li>
            <li>
                <a wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'withTrashed'})">
                    @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
                </a>
            </li>
            <li>
                <a wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'onlyTrashed'})">
                    @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
                </a>
            </li>
        </ul>
    </div>
@endif
