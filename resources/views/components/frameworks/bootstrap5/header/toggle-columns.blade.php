<div class="dropdown">
    @if (data_get($setUp, 'header.toggleColumns'))
        <div class="btn-group">
            <button
                data-cy="toggle-columns-{{ $tableName }}"
                class="btn btn-secondary dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <x-livewire-powergrid::icons.eye-off width="18" />
            </button>
            <ul class="dropdown-menu">
                @foreach ($this->visibleColumns as $column)
                    <li
                        wire:key="toggle-column-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                        data-cy="toggle-field-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                        wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})"
                    >
                        <a
                            class="dropdown-item"
                            href="#"
                        >
                            @if (data_get($column, 'hidden') === false)
                                <x-livewire-powergrid::icons.eye width="18" />
                            @else
                                <x-livewire-powergrid::icons.eye-off width="18" />
                            @endif
                            {!! data_get($column, 'title') !!}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
