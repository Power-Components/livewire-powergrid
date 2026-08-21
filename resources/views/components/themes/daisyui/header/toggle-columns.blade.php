@if (data_get($setUp, 'header.toggleColumns'))
    @php($element = ($__partial ?? $this)->headerElement('toggleColumns'))
    <div wire:key="toggle-columns-container-{{ $tableName }}" class="{{ theme('header.toggle_columns.wrapper') }}">
        <button
            data-cy="toggle-columns-{{ $tableName }}"
            class="{{ theme('header.toggle_columns.button', theme('header.layout.actions')) }}"
            popovertarget="toggle-popover-{{ $tableName }}"
            style="anchor-name: --toggle-{{ $tableName }}"
            title="{{ $element['title'] }}"
            aria-label="{{ $element['title'] }}"
        >
            {!! $element['iconHtml'] !!}
            @if ($element['showLabel'])
                <span class="{{ theme('header.toggle_columns.label') }}">{{ $element['title'] }}</span>
            @endif
        </button>
        <div
            id="toggle-popover-{{ $tableName }}"
            popover="auto"
            class="dropdown"
            style="position-anchor: --toggle-{{ $tableName }}"
        >
            <ul class="{{ theme('header.toggle_columns.menu') }}">
            @foreach ($this->visibleColumns as $column)
                <li wire:key="toggle-column-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                    data-cy="toggle-field-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                >
                    <a wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})" class="{{ theme('header.toggle_columns.menu_item') }} {{ data_get($column, 'hidden') ? 'opacity-50' : '' }}">
                        {!! data_get($column, 'title') !!}
                    </a>
                </li>
            @endforeach
            </ul>
        </div>
    </div>
@endif
