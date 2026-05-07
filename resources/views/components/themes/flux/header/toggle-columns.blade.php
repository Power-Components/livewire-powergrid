<div wire:key="toggle-columns-dropdown-{{ $tableName }}">
    @if (data_get($setUp, 'header.toggleColumns'))
        <flux:dropdown>
            <flux:button
                icon:trailing="chevron-down"
                class="{{ theme('header.export.button') }}"
            >
                <x-livewire-powergrid::icons.eye-off class="h-5 w-5" />
            </flux:button>

            <flux:menu>
                @foreach ($this->visibleColumns as $column)
                    <flux:menu.item
                        wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})"
                    >
                        <span class="{{ data_get($column, 'hidden') ? 'opacity-50' : '' }}">
                            {!! data_get($column, 'title') !!}
                        </span>
                    </flux:menu.item>
                @endforeach
            </flux:menu>
        </flux:dropdown>
    @endif
</div>
