@if (data_get($setUp, 'header.softDeletes'))
    <div class="btn-group">
        <button
            class="btn btn-secondary btn-sm dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            <span>
                <x-livewire-powergrid::icons.trash />
            </span>
        </button>
        <ul class="dropdown-menu">
            <li wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: ''})">
                <a
                    class="dropdown-item"
                    href="#"
                >
                    @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
                </a>
            </li>
            <li wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'withTrashed'})">
                <a
                    class="dropdown-item"
                    href="#"
                >
                    @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
                </a>
            </li>
            <li wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'onlyTrashed'})">
                <a
                    class="dropdown-item"
                    href="#"
                >
                    @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
                </a>
            </li>
        </ul>
    </div>
@endif
