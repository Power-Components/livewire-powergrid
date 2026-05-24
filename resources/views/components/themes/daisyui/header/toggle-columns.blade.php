@if (data_get($setUp, 'header.toggleColumns'))
    <div wire:key="toggle-columns-container-{{ $tableName }}">
        <button
            data-cy="toggle-columns-{{ $tableName }}"
            class="{{ theme('header.layout.actions') }}"
            popovertarget="toggle-popover-{{ $tableName }}"
            style="anchor-name: --toggle-{{ $tableName }}"
        >
            <x-livewire-powergrid::icons.eye-off class="w-4 h-4" />
        </button>
        <div
            id="toggle-popover-{{ $tableName }}"
            popover="auto"
            class="dropdown"
            style="position-anchor: --toggle-{{ $tableName }}"
        >
            <ul class="menu p-2 shadow bg-base-100 rounded-box w-52 mt-2 text-sm">
            @foreach ($this->visibleColumns as $column)
                <li wire:key="toggle-column-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                    data-cy="toggle-field-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                >
                    <a wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})" class="text-sm {{ data_get($column, 'hidden') ? 'opacity-50' : '' }}">
                        {!! data_get($column, 'title') !!}
                    </a>
                </li>
            @endforeach
            </ul>
        </div>
    </div>
@endif
