@props([
    'element' => [],
    'tableName' => null,
    'columns' => [],
    'operatorLabels' => [],
    'valuelessOperators' => [],
    'rangeOperators' => [],
    'applied' => ['match' => 'and', 'rows' => []],
    'matchDefault' => 'and',
    'appliedCount' => 0,
])

@php
    $params = [
        'tableName' => $tableName,
        'columns' => $columns,
        'operatorLabels' => $operatorLabels,
        'valueless' => array_values($valuelessOperators),
        'range' => array_values($rangeOperators),
        'match' => $matchDefault,
        'applied' => $applied,
    ];
@endphp

<div wire:key="pg-filter-builder-{{ $tableName }}">
    <flux:modal.trigger name="pg-filter-builder-{{ $tableName }}">
        <flux:button
            variant="filled"
            class="{{ theme('header.filter_builder.button', 'relative') }}"
            title="{{ data_get($element, 'title') }}"
            aria-label="{{ data_get($element, 'title') }}"
        >
            <span class="inline-flex items-center gap-1.5">
                {!! data_get($element, 'iconHtml') !!}
                @if (data_get($element, 'showLabel'))
                    <span class="{{ theme('header.filter_builder.label') }}">{{ data_get($element, 'title') }}</span>
                @endif
                @if ($appliedCount)
                    <span
                        class="{{ theme('header.filter_builder.badge') }}"
                    >{{ $appliedCount }}</span>
                @endif
            </span>
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="pg-filter-builder-{{ $tableName }}" class="w-full max-w-2xl md:max-w-4xl">
        <div x-data="pgFilterBuilder" data-pg-params="{{ json_encode($params) }}" class="flex w-full min-w-0 flex-col gap-6">
            <flux:heading size="lg">
                {{ trans('livewire-powergrid::datatable.filter_builder.title') }}
            </flux:heading>

            <div class="min-w-0 rounded-xl border border-zinc-200 p-3 dark:border-zinc-700/70 sm:p-4">
                <div class="flex flex-col gap-2.5">
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="flex min-w-0 items-center gap-2">
                            {{-- Per-row AND/OR connector (absent on the first row) --}}
                            <template x-if="notFirst(index)">
                                <div class="w-[4.5rem] shrink-0">
                                    <flux:select
                                        size="sm"
                                        x-model="row.boolean"
                                        class="w-full font-medium"
                                        aria-label="{{ trans('livewire-powergrid::datatable.filter_builder.connector') }}"
                                    >
                                        <flux:select.option value="and">{{ trans('livewire-powergrid::datatable.filter_builder.and') }}</flux:select.option>
                                        <flux:select.option value="or">{{ trans('livewire-powergrid::datatable.filter_builder.or') }}</flux:select.option>
                                    </flux:select>
                                </div>
                            </template>
                            {{-- Keep columns aligned with the connector rows --}}
                            <template x-if="isFirst(index)">
                                <span class="w-[4.5rem] shrink-0" aria-hidden="true"></span>
                            </template>

                            <div class="min-w-0 flex-1">
                                <flux:select
                                    size="sm"
                                    x-model="row.column"
                                    x-on:change="onColumnChange(row)"
                                    class="w-full"
                                    aria-label="{{ trans('livewire-powergrid::datatable.filter_builder.column_placeholder') }}"
                                >
                                    <template x-for="col in columns" :key="col.field">
                                        <option :value="col.field" x-text="col.title"></option>
                                    </template>
                                </flux:select>
                            </div>

                            <div class="min-w-0 flex-1">
                                <flux:select
                                    size="sm"
                                    x-model="row.operator"
                                    class="w-full"
                                    aria-label="{{ trans('livewire-powergrid::datatable.filter_builder.operator_placeholder') }}"
                                >
                                    <template x-for="op in operatorsFor(row.column)" :key="op">
                                        <option :value="op" x-text="operatorLabel(op)"></option>
                                    </template>
                                </flux:select>
                            </div>

                            <div class="flex min-w-0 flex-[1.6] flex-wrap items-center gap-2">
                                <template x-if="needsNoValue(row.operator)">
                                    <span class="w-full truncate text-xs italic text-zinc-400">
                                        {{ trans('livewire-powergrid::datatable.filter_builder.no_value') }}
                                    </span>
                                </template>

                                <template x-if="showValueSelect(row)">
                                    <div class="min-w-0 flex-1">
                                        <flux:select size="sm" x-model="row.value" class="w-full">
                                            <option value="">{{ trans('livewire-powergrid::datatable.filter_builder.value_placeholder') }}</option>
                                            <template x-for="opt in optionsFor(row.column)" :key="opt.value">
                                                <option :value="opt.value" x-text="opt.label"></option>
                                            </template>
                                        </flux:select>
                                    </div>
                                </template>

                                <template x-if="showValueInput(row)">
                                    <div class="min-w-[8rem] flex-1">
                                        <flux:input
                                            size="sm"
                                            ::type="inputType(row.column)"
                                            x-model="row.value"
                                            placeholder="{{ trans('livewire-powergrid::datatable.filter_builder.value_placeholder') }}"
                                            class="w-full"
                                        />
                                    </div>
                                </template>

                                <template x-if="needsRange(row.operator)">
                                    <div class="min-w-[8rem] flex-1">
                                        <flux:input
                                            size="sm"
                                            ::type="inputType(row.column)"
                                            x-model="row.value2"
                                            placeholder="{{ trans('livewire-powergrid::datatable.filter_builder.value_to') }}"
                                            class="w-full"
                                        />
                                    </div>
                                </template>
                            </div>

                            <flux:button
                                variant="subtle"
                                size="sm"
                                icon="x-mark"
                                type="button"
                                x-on:click="removeRow(index)"
                                class="shrink-0 text-red-500 hover:text-red-600"
                                aria-label="{{ trans('livewire-powergrid::datatable.filter_builder.remove') }}"
                            />
                        </div>
                    </template>
                </div>

                <div class="mt-4 flex justify-end">
                    <flux:button variant="ghost" size="sm" icon="plus" type="button" x-on:click="addRow()">
                        {{ trans('livewire-powergrid::datatable.filter_builder.add_condition') }}
                    </flux:button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                <flux:button variant="ghost" type="button" x-on:click="reset()">
                    {{ trans('livewire-powergrid::datatable.filter_builder.reset') }}
                </flux:button>
                <flux:button variant="primary" type="button" x-on:click="apply()">
                    {{ trans('livewire-powergrid::datatable.filter_builder.apply') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
