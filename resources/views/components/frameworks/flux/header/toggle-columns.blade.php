@if (data_get($setUp, 'header.toggleColumns'))
    <flux:dropdown>
        <flux:button
            icon:trailing="chevron-down"
            class="{{ theme('header.toggle_columns.button') }}"
        >
            <x-livewire-powergrid::icons.eye-off class="w-5 h-5" />
        </flux:button>

        <flux:menu class="w-48">
            @foreach ($this->visibleColumns as $column)
                <flux:menu.checkbox keep-open
                    wire:key="toggle-column-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                    :checked="!data_get($column, 'hidden')"
                    wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})"
                >
                    {!! data_get($column, 'title') !!}
                </flux:menu.checkbox>
            @endforeach
        </flux:menu>
    </flux:dropdown>
@endif
