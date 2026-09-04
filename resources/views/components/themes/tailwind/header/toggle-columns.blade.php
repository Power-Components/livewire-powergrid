@if (data_get($setUp, 'header.toggleColumns'))
    @php
        $__partial = $__partial ?? $this;
        $tableName = $tableName ?? $__partial->tableName;
        $element = $__partial->headerElement('toggleColumns');
        $hiddenCount = $__partial->hiddenColumnsCount();
    @endphp
    <div
        x-data="{ open: false }"
        wire:key="pg-toggle-columns-{{ $tableName }}"
        class="relative {{ theme('filter.dropdown.wrapper') }}"
    >
        <button
            type="button"
            data-cy="toggle-columns-{{ $tableName }}"
            x-on:click="open = ! open"
            title="{{ $element['title'] }}"
            aria-label="{{ $element['title'] }}"
            class="{{ theme('header.toggle_columns.button', theme('header.layout.actions')) }} relative"
        >
            <div class="flex items-center">
                {!! $element['iconHtml'] !!}
                @if ($element['showLabel'])
                    <span class="{{ theme('header.toggle_columns.label') }}">{{ $element['title'] }}</span>
                @endif
            </div>
            @if ($hiddenCount)
                <span
                    data-cy="toggle-columns-badge"
                    class="{{ theme('filter.dropdown.badge') }}"
                >{{ $hiddenCount }}</span>
            @endif
        </button>

        <div
            x-show="open"
            x-cloak
            style="display: none"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            x-on:click.outside="open = false"
            x-on:keydown.escape.window="open = false"
            role="dialog"
            data-cy="toggle-columns-panel"
            class="{{ theme('header.toggle_columns.panel', theme('filter.dropdown.panel')) }}"
        >
            <div class="{{ theme('filter.dropdown.header') }}">
                <span class="{{ theme('filter.dropdown.title') }}">{{ $element['title'] }}</span>
            </div>

            <div class="{{ theme('filter.dropdown.body') }} space-y-3">
                @foreach ($__partial->visibleColumns as $column)
                    @php($field = data_get($column, 'field'))
                    @if (is_string($field) && $field !== '' && ! data_get($column, 'isAction'))
                        <label
                            wire:key="toggle-column-{{ $field }}"
                            class="flex items-center gap-2.5 cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                data-cy="toggle-field-{{ $field }}"
                                wire:model="draftColumns.{{ $field }}"
                                class="{{ theme('header.toggle_columns.checkbox', theme('table.checkbox.input')) }}"
                            >
                            <span class="text-sm text-zinc-700 dark:text-zinc-200">{!! data_get($column, 'title') !!}</span>
                        </label>
                    @endif
                @endforeach
            </div>

            <div class="{{ theme('filter.dropdown.footer') }}">
                <button
                    type="button"
                    data-cy="toggle-columns-reset"
                    wire:click.prevent="resetColumns"
                    class="{{ theme('filter.dropdown.reset') }} me-auto"
                >
                    {{ trans('livewire-powergrid::datatable.buttons.reset_filters') }}
                </button>

                <button
                    type="button"
                    data-cy="toggle-columns-apply"
                    wire:click.prevent="applyColumns"
                    x-on:click="open = false"
                    class="{{ theme('filter.dropdown.apply') }}"
                >
                    {{ trans('livewire-powergrid::datatable.buttons.apply_columns') }}
                </button>
            </div>
        </div>
    </div>
@endif
