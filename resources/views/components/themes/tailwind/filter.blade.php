@props([
    'columns' => null,
    'theme' => null,
    'tableName' => null,
    'filtersFromColumns' => null,
    'showFilters' => false,
    '__partial' => null,
])

@php
    $__partial = $__partial ?? (isset($this) ? $this : null);
@endphp

<div
    x-data
    class="mt-2 md:mt-0"
>
    <div
        x-show="$wire.showFilters"
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

            $filtersFromColumns = collect($filtersFromColumns ?? [])
                ->filter(fn($column) => filled(data_get($column, 'filters')));

            if ($filtersFromColumns->isEmpty() && $__partial) {
                 $filtersFromColumns = collect($__partial->columns)
                    ->filter(fn($column) => filled(data_get($column, 'filters')));
            }

            $componentFilters = collect($__partial ? $__partial->filters() : []);
            $filterOrderMap = $componentFilters->pluck('field')->flip();

            // Sort filters based on the order they appear in filters() method
            $sortedFilters = $filtersFromColumns->sortBy(function ($column) use ($filterOrderMap) {
                $fieldName = data_get($column, 'filters.field');
                return $filterOrderMap->get($fieldName, 999); // 999 for fields not found in filters()
            });
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-3">
            @foreach ($sortedFilters as $column)
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
                            :theme="$theme"
                            :table-name="$tableName"
                            :filter="$filter"
                            :title="$title"
                            :initial-values="data_get(data_get($filter, 'multi_select'), data_get($filter, 'field'), [])"
                            :__partial="$__partial"
                        />
                    @elseif ($className->contains(['FilterDateTimePicker', 'FilterDatePicker']))
                        @includeIf(theme_view('filter.date_picker'), [
                            'filter' => $filter,
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                            'type' => $className->contains('FilterDateTimePicker') ? 'datetime' : 'date',
                            '__partial' => $__partial,
                        ])
                    @elseif ($className->contains(['FilterSelect', 'FilterEnumSelect']))
                        @includeIf(theme_view('filter.select'), [
                            'filter' => $filter,
                            '__partial' => $__partial,
                        ])
                    @elseif ($className->contains('FilterNumber'))
                        @includeIf(theme_view('filter.number'), [
                            'filter' => $filter,
                            '__partial' => $__partial,
                        ])
                    @elseif ($className->contains('FilterInputText'))
                        @includeIf(theme_view('filter.input_text'), [
                            'filter' => $filter,
                            '__partial' => $__partial,
                        ])
                    @elseif ($className->contains('FilterBoolean'))
                        @includeIf(theme_view('filter.boolean'), [
                            'filter' => $filter,
                            '__partial' => $__partial,
                        ])
                    @elseif ($className->contains('FilterDynamic'))
                        <x-dynamic-component
                            :component="data_get($filter, 'component', '')"
                            :attributes="new \Illuminate\View\ComponentAttributeBag(
                                array_merge(
                                    data_get($filter, 'attributes', []),
                                    ['__partial' => $__partial]
                                ),
                            )"
                        />
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
