@props([
    'columns' => null,
    'theme' => null,
    'tableName' => null,
    'filtersFromColumns' => null,
    'showFilters' => false,
])
<div
    x-data="{ open: @entangle('showFilters').live }"
    class="mt-2 md:mt-0"
>
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transform duration-100"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transform duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="py-3"
        wire:key="filter-{{ uniqid() }}"
    >
        @php
            $customConfig = [];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-3">
            @foreach ($filtersFromColumns as $filters)
                @foreach ($filters as $filter)
                    @if (str(data_get($filter, 'className'))->contains('FilterMultiSelect'))
                        <div class="{{ data_get($filter, 'baseClass') }}">
                            <x-livewire-powergrid::inputs.select
                                :inline="false"
                                :tableName="$tableName"
                                :filter="$filter"
                                :theme="$theme->filterMultiSelect"
                                :initialValues="data_get(data_get($filters, 'multi_select'), data_get($filter, 'field'), [])"
                            />
                        </div>
                    @endif
                    @if (str(data_get($filter, 'className'))->contains('FilterDateTimePicker'))
                        <div class="{{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterDatePicker->view, [
                                'filter' => $filter,
                                'tableName' => $tableName,
                                'classAttr' => 'w-full',
                                'theme' => $theme->filterDatePicker,
                                'type' => 'datetime',
                            ])
                        </div>
                    @endif
                    @if (str(data_get($filter, 'className'))->contains('FilterDatePicker'))
                        <div class="{{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterDatePicker->view, [
                                'filter' => $filter,
                                'tableName' => $tableName,
                                'classAttr' => 'w-full',
                                'theme' => $theme->filterDatePicker,
                                'type' => 'date',
                            ])
                        </div>
                    @endif
                    @if (str(data_get($filter, 'className'))->contains(['FilterSelect', 'FilterEnumSelect']))
                        <div class="{{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterSelect->view, [
                                'filter' => $filter,
                                'theme' => $theme->filterSelect,
                            ])
                        </div>
                    @endif
                    @if (str(data_get($filter, 'className'))->contains('FilterNumber'))
                        <div class="{{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterNumber->view, [
                                'filter' => $filter,
                                'theme' => $theme->filterNumber,
                            ])
                        </div>
                    @endif
                    @if (str(data_get($filter, 'className'))->contains('FilterInputText'))
                        <div class="{{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterInputText->view, [
                                'filter' => $filter,
                                'theme' => $theme->filterInputText,
                            ])
                        </div>
                    @endif
                    @if (str(data_get($filter, 'className'))->contains('FilterBoolean'))
                        <div class="{{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterBoolean->view, [
                                'filter' => $filter,
                                'theme' => $theme->filterBoolean,
                            ])
                        </div>
                    @endif
                    @if (str(data_get($filter, 'className'))->contains('FilterDynamic'))
                        <div class="{{ data_get($filter, 'baseClass') }}">
                            <x-dynamic-component
                                :component="data_get($filter, 'component', '')"
                                :attributes="new \Illuminate\View\ComponentAttributeBag(
                                    data_get($filter, 'attributes', []),
                                )"
                            />
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
</div>
