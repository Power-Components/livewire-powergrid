@props([
    'columns' => null,
    'theme' => null,
    'tableName' => null,
    'filtersFromColumns' => null,
])
<div x-data="{ open:@entangle('showFilters') }"
     x-on:toggle-filters-{{ $tableName }}.window="open = !open"
     class="mt-2 md:mt-0">
    <div x-show="open"
         x-cloak
         x-transition:enter="transform duration-100"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transform duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="py-3">
        @php
            $customConfig = [];
        @endphp
        <div class="md:flex md:flex-wrap gap-3 space-y-3 md:space-y-0">
            @foreach($filtersFromColumns as $filters)
                @foreach($filters as $filter)
                    @if(str(data_get($filter, 'className'))->contains('FilterMultiSelect'))
                        <div class="flex-1 {{ data_get($filter, 'baseClass') }}">
                            <x-livewire-powergrid::inputs.select
                                    :inline="false"
                                    :tableName="$tableName"
                                    :filter="$filter"
                                    :theme="$theme->filterMultiSelect"
                                    :initialValues="data_get(data_get($filters, 'multi_select'), data_get($filter, 'field'), [])"/>
                        </div>
                    @endif
                    @if(str(data_get($filter, 'className'))->contains('FilterDateTimePicker'))
                        <div class="flex-1 {{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterDatePicker->view, [
                                'filter'    => $filter,
                                'tableName' => $tableName,
                                'classAttr' => 'w-full',
                                'theme'     => $theme->filterDatePicker,
                            ])
                        </div>
                    @endif
                        @if(str(data_get($filter, 'className'))->contains('FilterDatePicker'))
                            <div class="flex-1 {{ data_get($filter, 'baseClass') }}">
                                @includeIf($theme->filterDatePicker->view, [
                                    'filter'    => $filter,
                                    'tableName' => $tableName,
                                    'classAttr' => 'w-full',
                                    'theme'     => $theme->filterDatePicker,
                                ])
                            </div>
                        @endif
                    @if(str(data_get($filter, 'className'))->contains(['FilterSelect', 'FilterEnumSelect']))
                        <div class="flex-1 {{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterSelect->view, [
                               'filter' => $filter,
                               'theme' => $theme->filterSelect,
                            ])
                        </div>
                    @endif
                    @if(str(data_get($filter, 'className'))->contains('FilterNumber'))
                        <div class="flex-1 {{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterNumber->view, [
                                'filter'           => $filter,
                                'theme'            => $theme->filterNumber,
                            ])
                        </div>
                    @endif
                    @if(str(data_get($filter, 'className'))->contains('FilterInputText'))
                        <div class="flex-1 {{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterInputText->view, [
                               'filter'           => $filter,
                               'theme'            => $theme->filterInputText,
                            ])
                        </div>
                    @endif
                    @if(str(data_get($filter, 'className'))->contains('FilterBoolean'))
                        <div class="flex-1 {{ data_get($filter, 'baseClass') }}">
                            @includeIf($theme->filterBoolean->view, [
                               'filter'           => $filter,
                               'theme'            => $theme->filterBoolean,
                            ])
                        </div>
                    @endif
                    @if(str(data_get($filter, 'className'))->contains('FilterDynamic'))
                        <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($filter, 'baseClass') }}">
                            <x-dynamic-component :component="data_get($filter, 'component', '')"
                                                 :attributes="new \Illuminate\View\ComponentAttributeBag(data_get($filter, 'attributes', []))" />
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
</div>
