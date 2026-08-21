@if (data_get($setUp, 'header.toggleColumns'))
    @php($element = ($__partial ?? $this)->headerElement('toggleColumns'))
    <div
        x-data="pgDropdown"
        @click.outside="close()"
        class="{{ theme('header.toggle_columns.wrapper') }}"
    >
        <button
            data-cy="toggle-columns-{{ $tableName }}"
            @click.prevent="toggle()"
            title="{{ $element['title'] }}"
            aria-label="{{ $element['title'] }}"
            class="{{ theme('header.toggle_columns.button', theme('header.layout.actions')) }}"
        >
            <div class="flex items-center">
                {!! $element['iconHtml'] !!}
                @if ($element['showLabel'])
                    <span class="{{ theme('header.toggle_columns.label') }}">{{ $element['title'] }}</span>
                @endif
            </div>
        </button>

        <div
            x-cloak
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="{{ theme('header.toggle_columns.menu') }}"
            tabindex="-1"
            @keydown.tab="close()"
            @keydown.enter.prevent="close()"
            @keyup.space.prevent="close()"
        >
            <div
                role="none"
            >
                @foreach ($this->visibleColumns as $column)
                    <div
                        wire:key="toggle-column-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                        data-cy="toggle-field-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                        x-on:click="dispatch('pg:toggleColumn-{{ $tableName }}', 'field', '{{ data_get($column, 'field') }}')"
                        @class([
                            'font-semibold bg-zinc-100 dark:bg-zinc-800 ' => data_get($column, 'hidden'),
                            'py-1' => $loop->first || $loop->last,
                            theme('header.toggle_columns.menu_item')
                        ])
                    >
                        <div>
                            {!! data_get($column, 'title') !!}
                        </div>
                        @if (!data_get($column, 'hidden'))
                            <x-livewire-powergrid::icons.eye class="h-5 w-5 text-zinc-200 dark:text-zinc-300 shrink-0" />
                        @else
                            <x-livewire-powergrid::icons.eye-off class="h-5 w-5 text-zinc-500 dark:text-zinc-300 shrink-0" />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
