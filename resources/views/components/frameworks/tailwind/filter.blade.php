@props([
    'columns' => null,
    'theme' => null,
    'tableName' => null,
    'filtersFromColumns' => null,
])
<div class="w-full my-3 dark:bg-pg-primary-800">
    <div x-data={show:true} class="rounded-full">
        <div
            class="px-4 py-3 rounded-t-md cursor-pointer bg-pg-primary-100 border border-pg-primary-200 dark:border-pg-primary-600 dark:bg-pg-primary-600"
            @click="show=!show">
            <button
                class="appearance-none text-left text-base font-medium text-pg-primary-500 focus:outline-none dark:text-pg-primary-300"
                type="button">
                {{ trans('livewire-powergrid::datatable.buttons.filter') }}
            </button>
        </div>
        <div x-show="show"
             class="border-l rounded-b-md border-b border-r border-pg-primary-200 dark:border-pg-primary-600 px-2 py-4 dark:border-0 dark:bg-pg-primary-700">
            <div>
                @php
                    $customConfig = [];
                @endphp
                <div class="md:flex md:flex-wrap">
                    @foreach($filtersFromColumns as $filters)
                        @foreach($filters as $filter)
                            @if(str(data_get($filter, 'className'))->contains('FilterMultiSelect'))
                                <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($filter, 'baseClass') }}">
                                    <x-livewire-powergrid::inputs.select
                                        :inline="false"
                                        :tableName="$tableName"
                                        :filter="$filter"
                                        :theme="$theme->filterMultiSelect"
                                        :initialValues="data_get(data_get($filters, 'multi_select'), data_get($filter, 'field'), [])"/>
                                </div>
                            @endif
                            @if(str(data_get($filter, 'className'))->contains('FilterDatePicker'))
                                    <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($filter, 'baseClass') }}">
                                        @includeIf($theme->filterDatePicker->view, [
                                            'filter'    => $filter,
                                            'tableName' => $tableName,
                                            'classAttr' => 'w-full',
                                            'theme'     => $theme->filterDatePicker,
                                        ])
                                    </div>
                                @endif
                                @if(str(data_get($filter, 'className'))->contains(['FilterSelect', 'FilterEnumSelect']))
                                    <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($filter, 'baseClass') }}">
                                        @includeIf($theme->filterSelect->view, [
                                           'filter' => $filter,
                                           'theme' => $theme->filterSelect,
                                        ])
                                    </div>
                                @endif
                                @if(str(data_get($filter, 'className'))->contains('FilterNumber'))
                                    <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($filter, 'baseClass') }}">
                                        @includeIf($theme->filterNumber->view, [
                                            'filter'           => $filter,
                                            'theme'            => $theme->filterNumber,
                                        ])
                                    </div>
                                @endif
                                @if(str(data_get($filter, 'className'))->contains('FilterInputText'))
                                    <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($filter, 'baseClass') }}">
                                        @includeIf($theme->filterInputText->view, [
                                           'filter'           => $filter,
                                           'theme'            => $theme->filterInputText,
                                        ])
                                    </div>
                                @endif
                                @if(str(data_get($filter, 'className'))->contains('FilterBoolean'))
                                    <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($filter, 'baseClass') }}">
                                        @includeIf($theme->filterBoolean->view, [
                                           'filter'           => $filter,
                                           'theme'            => $theme->filterBoolean,
                                        ])
                                    </div>
                                @endif
                                @if(str(data_get($filter, 'className'))->contains('FilterMultiSelect'))
                                    <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($filter, 'baseClass') }}">
                                        <x-livewire-powergrid::inputs.select
                                            :inline="false"
                                            :tableName="$tableName"
                                            :filter="$filter"
                                            :theme="$theme->filterMultiSelect"
                                            :initialValues="data_get(data_get($filters, 'multi_select'), data_get($filter, 'field'), [])"/>
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
    </div>
</div>
