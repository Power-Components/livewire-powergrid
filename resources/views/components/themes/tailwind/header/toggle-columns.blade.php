@if (data_get($setUp, 'header.toggleColumns'))
    @php
        $__partial = $__partial ?? $this;
        $tableName = $tableName ?? $__partial->tableName;
        $element = $__partial->headerElement('toggleColumns');
        $toggleable = fn ($column) => is_string(data_get($column, 'field'))
            && data_get($column, 'field') !== ''
            && ! data_get($column, 'isAction')
            && ! data_get($column, 'forceHidden');
        $columnState = fn ($columns) => collect($columns)->filter($toggleable)->map(fn ($column) => [
            'field' => data_get($column, 'field'),
            'hidden' => (bool) data_get($column, 'hidden'),
        ])->values()->all();
        $reorderLabel = trans('livewire-powergrid::datatable.labels.reorder_column');
    @endphp
    {{-- Keyed by column state: a server-side change re-initialises the client draft. --}}
    <div
        x-data="pgToggleColumns(@js($columnState($__partial->declaredColumns())))"
        wire:key="pg-toggle-columns-{{ $tableName }}-{{ md5((string) json_encode($columnState($__partial->columns))) }}"
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
            <span
                x-show="hiddenCount"
                x-text="hiddenCount"
                x-cloak
                data-cy="toggle-columns-badge"
                class="{{ theme('filter.dropdown.badge') }}"
            ></span>
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

            <div
                x-ref="list"
                x-sort
                wire:ignore
                class="{{ theme('filter.dropdown.body') }} space-y-3"
            >
                @foreach ($__partial->visibleColumns as $column)
                    @php($field = data_get($column, 'field'))
                    @if ($toggleable($column))
                        <div
                            x-sort:item
                            data-pg-toggle-item
                            data-field="{{ $field }}"
                            data-column="{{ data_get($column, 'dataField') ?: $field }}"
                            class="flex items-center gap-2.5"
                        >
                            <button
                                type="button"
                                x-sort:handle
                                x-on:keydown.up.prevent="move($el, -1)"
                                x-on:keydown.down.prevent="move($el, 1)"
                                data-cy="toggle-handle-{{ $field }}"
                                title="{{ $reorderLabel }}"
                                aria-label="{{ $reorderLabel }}"
                                class="{{ theme('header.toggle_columns.handle', 'cursor-grab rounded text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200') }}"
                            >
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <circle cx="5.5" cy="3.5" r="1.5" /><circle cx="10.5" cy="3.5" r="1.5" />
                                    <circle cx="5.5" cy="8" r="1.5" /><circle cx="10.5" cy="8" r="1.5" />
                                    <circle cx="5.5" cy="12.5" r="1.5" /><circle cx="10.5" cy="12.5" r="1.5" />
                                </svg>
                            </button>
                            <label class="flex flex-1 items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    data-cy="toggle-field-{{ $field }}"
                                    @checked(! data_get($column, 'hidden'))
                                    class="{{ theme('header.toggle_columns.checkbox', theme('table.checkbox.input')) }}"
                                >
                                <span class="{{ theme('header.toggle_columns.item_label', 'text-sm text-zinc-700 dark:text-zinc-200') }}">{!! data_get($column, 'title') !!}</span>
                            </label>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="{{ theme('filter.dropdown.footer') }}">
                <button
                    type="button"
                    data-cy="toggle-columns-reset"
                    x-on:click="reset()"
                    class="{{ theme('filter.dropdown.reset') }} me-auto"
                >
                    {{ trans('livewire-powergrid::datatable.buttons.reset_filters') }}
                </button>

                <button
                    type="button"
                    data-cy="toggle-columns-apply"
                    x-on:click="apply()"
                    class="{{ theme('filter.dropdown.apply') }}"
                >
                    {{ trans('livewire-powergrid::datatable.buttons.apply_columns') }}
                </button>
            </div>
        </div>
    </div>
@endif
