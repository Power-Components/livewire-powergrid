@if (data_get($setUp, 'header.toggleColumns'))
    <div class="{{ theme('header.toggle_columns.container') }}">
        <div tabindex="0" role="button" data-cy="toggle-columns-{{ $tableName }}" class="{{ theme('header.toggle_columns.button') }}">
            <x-livewire-powergrid::icons.eye-off class="w-4 h-4" />
        </div>
        <ul tabindex="0" class="{{ theme('header.toggle_columns.menu') }}">
            @foreach ($this->visibleColumns as $column)
                <li wire:key="toggle-column-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                    data-cy="toggle-field-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                    class="{{ theme('header.toggle_columns.menu_item') }}"
                >
                    <a wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})" class="{{ data_get($column, 'hidden') ? 'opacity-50' : '' }}">
                        <div class="flex-1">
                            {!! data_get($column, 'title') !!}
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
