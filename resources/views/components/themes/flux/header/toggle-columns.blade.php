@if (data_get($setUp, 'header.toggleColumns'))
    <div wire:key="toggle-columns-dropdown-{{ $tableName }}">
        <flux:dropdown>
            <flux:button variant="filled" class="!w-12 !h-10 !flex !items-center !justify-center">
                <x-livewire-powergrid::icons.eye-off class="w-6 h-6" />
            </flux:button>

            <flux:menu class="dark:bg-zinc-900">
                @foreach ($this->visibleColumns as $column)
                    <flux:menu.item
                        wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})"
                    >
                        <span @class(['opacity-40' => data_get($column, 'hidden')])>
                            {!! data_get($column, 'title') !!}
                        </span>
                    </flux:menu.item>
                @endforeach
            </flux:menu>
        </flux:dropdown>
    </div>
@endif
