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
        class="pg-filter-container"
    >
        @php
            $customConfig = [];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-3">
            @foreach ($filtersFromColumns as $column)
                @php
                    $filter = data_get($column, 'filters');
                    $title = data_get($column, 'title');
                    $baseClass = data_get($filter, 'baseClass');
                    $className = str(data_get($filter, 'className'));
                @endphp

                <div class="{{ $baseClass }}">
                    @if ($className->contains('FilterMultiSelect'))
                        <x-livewire-powergrid::inputs.select
                                :inline="false"
                                :table-name="$tableName"
                                :filter="$filter"
                                :theme="data_get($theme, 'filterMultiSelect')"
                                :title="$title"
                                :initial-values="data_get(data_get($filter, 'multi_select'), data_get($filter, 'field'), [])"
                        />
                    @elseif ($className->contains(['FilterDateTimePicker', 'FilterDatePicker']))
                        @includeIf(data_get($theme, 'filterDatePicker.view'), [
                            'filter' => $filter,
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                            'theme' => data_get($theme, 'filterDatePicker'),
                            'type' => $className->contains('FilterDateTimePicker') ? 'datetime' : 'date',
                        ])
                    @elseif ($className->contains(['FilterSelect', 'FilterEnumSelect']))
                        @includeIf(data_get($theme, 'filterSelect.view'), [
                            'filter' => $filter,
                            'theme' => data_get($theme, 'filterSelect'),
                        ])
                    @elseif ($className->contains('FilterNumber'))
                        @includeIf(data_get($theme, 'filterNumber.view'), [
                            'filter' => $filter,
                            'theme' => data_get($theme, 'filterNumber'),
                        ])
                    @elseif ($className->contains('FilterInputText'))
                        @includeIf(data_get($theme, 'filterInputText.view'), [
                            'filter' => $filter,
                            'theme' => data_get($theme, 'filterInputText'),
                        ])
                    @elseif ($className->contains('FilterBoolean'))
                        @includeIf(data_get($theme, 'filterBoolean.view'), [
                            'filter' => $filter,
                            'theme' => data_get($theme, 'filterBoolean'),
                        ])
                    @elseif ($className->contains('FilterDynamic'))
                        <x-dynamic-component
                                :component="data_get($filter, 'component', '')"
                                :attributes="new \Illuminate\View\ComponentAttributeBag(data_get($filter, 'attributes', []))"
                        />
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
