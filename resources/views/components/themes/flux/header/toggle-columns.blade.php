@if (data_get($setUp, 'header.toggleColumns'))
    @php($element = ($__partial ?? $this)->headerElement('toggleColumns'))
    <div wire:key="toggle-columns-dropdown-{{ $tableName }}" class="{{ theme('header.toggle_columns.wrapper') }}">
        <flux:dropdown>
            <flux:button
                variant="filled"
                class="{{ theme('header.toggle_columns.button', theme('header.layout.actions')) }}"
                title="{{ $element['title'] }}"
                aria-label="{{ $element['title'] }}"
                :icon="$element['isComponentPath'] ? null : ($element['icon'] ?: null)"
            >
                @if ($element['isComponentPath'] && $element['iconHtml'])
                    {!! $element['iconHtml'] !!}
                @endif
                @if ($element['showLabel'])
                    <span class="{{ theme('header.toggle_columns.label') }}">{{ $element['title'] }}</span>
                @endif
            </flux:button>

            <flux:menu class="{{ theme('header.toggle_columns.menu') }}">
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
