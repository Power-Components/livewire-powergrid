<div>
    @if (data_get($setUp, 'header.toggleColumns'))
        <div class="btn-group">
            <button
                class="btn btn-light dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <x-livewire-powergrid::icons.eye-off width="20" />
            </button>
            <ul class="dropdown-menu">
                @foreach ($this->visibleColumns as $column)
                    <li
                        wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})"
                        wire:key="toggle-column-{{ data_get($column, 'field') }}"
                    >
                        <a
                            class="dropdown-item"
                            href="#"
                        >
                            @if (data_get($column, 'hidden') === false)
                                <x-livewire-powergrid::icons.eye width="20" />
                            @else
                                <x-livewire-powergrid::icons.eye-off width="20" />
                            @endif
                            {!! data_get($column, 'title') !!}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
