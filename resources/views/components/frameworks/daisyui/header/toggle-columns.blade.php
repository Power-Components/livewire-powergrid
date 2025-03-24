@if (data_get($setUp, 'header.toggleColumns'))
    <div x-data="{ open: false, countChecked: @entangle('checkboxValues').live }">
        <div class="dropdown">
            <div
                tabindex="0"
                role="button"
                class="btn btn-sm btn-primary"
            ><x-livewire-powergrid::icons.eye-off class="w-5 h-5" /></div>
            <div
                tabindex="0"
                class="dropdown-content z-1 bg-base-300 shadow-md"
            >
                <div class="w-56">
                    <ul class="menu menu-md w-full">
                        @foreach ($this->visibleColumns as $column)
                            <li
                                wire:key="toggle-column-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                                data-cy="toggle-field-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                                wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})"
                                @class([
                                    '!bg-neutral !text-neutral-content' => data_get($column, 'hidden'),
                                ])
                            >
                                <a>{!! data_get($column, 'title') !!}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
