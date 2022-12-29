@props([
    'makeFilters' => null,
    'checkbox' => null,
    'columns' => null,
    'actions' => null,
    'theme' => null,
    'tableName' => null,
    'enabledFilters' => null,
    'inputTextOptions' => [],
    'filters' => null,
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
                    @foreach(data_get($makeFilters, 'date_picker', []) as $field => $date)
                        <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($date, 'baseClass') }}">
                            @includeIf($theme->filterDatePicker->view, [
                                 'inline'    => false,
                                 'date'      => $date,
                                 'tableName' => $tableName,
                                 'classAttr' => 'w-full',
                                 'theme'     => $theme->filterDatePicker,
                            ])
                        </div>
                    @endforeach
                    @foreach(data_get($makeFilters, 'select', []) as $field => $select)
                        <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($select, 'baseClass') }}">
                            @includeIf($theme->filterSelect->view, [
                                 'inline' => false,
                                 'select' => $select,
                                 'theme'  => $theme->filterSelect,
                            ])
                        </div>
                    @endforeach
                    @foreach(data_get($makeFilters, 'number', []) as $field => $number)
                        <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($number, 'baseClass') }}">
                            @includeIf($theme->filterNumber->view, [
                                'inline' => false,
                                'number' => $number,
                                'theme'  => $theme->filterNumber,
                            ])
                        </div>
                    @endforeach
                    @foreach(data_get($makeFilters, 'input_text', []) as $field => $inputText)
                        <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($inputText, 'baseClass') }}">
                            @includeIf($theme->filterInputText->view, [
                                 'inline'           => false,
                                 'enabledFilters'   => $enabledFilters,
                                 'inputTextOptions' => $inputTextOptions,
                                 'enabledFilters'   => $enabledFilters,
                                 'theme'            => $theme->filterInputText,
                            ])
                        </div>
                    @endforeach
                    @foreach(data_get($makeFilters, 'boolean', []) as $field => $booleanFilter)
                        <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($booleanFilter, 'baseClass') }}">
                            @includeIf($theme->filterBoolean->view, [
                                 'inline'         => false,
                                 'booleanFilter'  => $booleanFilter,
                                 'tableName'      => $tableName,
                                 'theme'          => $theme->filterBoolean,
                            ])
                        </div>
                    @endforeach
                    @foreach(data_get($makeFilters, 'multi_select', []) as $field => $multiSelect)
                        <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($booleanFilter, 'baseClass') }}">
                            <x-livewire-powergrid::inputs.select
                                :inline="false"
                                :multiSelect="$multiSelect"
                                :theme="$theme->filterMultiSelect"
                                :initialValues="data_get(data_get($filters, 'multi_select'), $multiSelect['dataField'], [])"
                                :tableName="$tableName" />
                        </div>
                    @endforeach
                    @foreach(data_get($makeFilters, 'dynamic', []) as $field => $input)
                        <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4 {{ data_get($input, 'baseClass') }}">
                            @include(powerGridThemeRoot().'.filters.dynamic', [
                                'input' => $input,
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
