@if (data_get($setUp, 'header.toggleColumns'))
    <div class="dropdown" :class="{ 'dropdown-open': open }" x-data="{ open: false }" x-on:keydown.esc="open = false" x-on:click.outside="open = false" wire:key="toggle-columns-dropdown-{{ $tableName }}">
        <div tabindex="0" role="button" data-cy="toggle-columns-{{ $tableName }}" class="{{ theme('header.layout.actions') }}" x-on:click="open = !open">
            <x-livewire-powergrid::icons.eye-off class="w-4 h-4" />
        </div>
        <ul tabindex="0" class="dropdown-content z-[50] menu p-2 shadow bg-base-100 rounded-box w-52 mt-2">
            @foreach ($this->visibleColumns as $column)
                <li wire:key="toggle-column-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                    data-cy="toggle-field-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
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
