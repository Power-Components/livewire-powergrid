@if (data_get($setUp, 'header.softDeletes'))
    <div
        class="dropdown"
    >
        <div
            tabindex="0"
            role="button"
            class="btn btn-sm btn-primary"
        >
            <x-livewire-powergrid::icons.trash class="h-5 w-5" />
        </div>
        <div
            tabindex="0"
            class="dropdown-content bg-base-100 z-1 shadow-md"
        >
            <ul class="menu bg-base-200 rounded-box w-56">
                <li>
                    <a x-on:click="$wire.dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: ''});">@lang('livewire-powergrid::datatable.soft_deletes.without_trashed')</a>
                </li>
                <li>
                    <a x-on:click="$wire.dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'withTrashed'});">@lang('livewire-powergrid::datatable.soft_deletes.with_trashed')</a>
                </li>
                <li>
                    <a x-on:click="$wire.dispatch('pg:softDeletes-{{ $tableName }}', {softDeletes: 'onlyTrashed'});">@lang('livewire-powergrid::datatable.soft_deletes.only_trashed')</a>
                </li>
            </ul>

        </div>
    </div>
@endif
