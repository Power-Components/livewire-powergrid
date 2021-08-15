@props([
    'makeFilters' => null,
    'checkbox' => null,
    'columns' => null,
    'actions' => null,
    'theme' => null
])
<div class="w-full pb-3 dark:bg-gray-800">
    <div x-data={show:true} class="rounded-sm">
        <div class="px-4 py-3 cursor-pointer bg-gray-200 shadow dark:bg-gray-500"
             @click="show=!show">
            <button
                class="appearance-none text-left text-base font-medium text-gray-500 focus:outline-none dark:text-gray-300"
                type="button">
                {{ trans('livewire-powergrid::datatable.buttons.filter') }}
            </button>
        </div>
        <div x-show="show" class="border-l border-b border-r border-gray-200 dark:border-gray-600 px-2 py-4 dark:border-0 dark:bg-gray-700">
            <div>
                @php
                    $customConfig = [];
                @endphp
                <div class="md:flex md:flex-wrap">

                    @if(isset($makeFilters['date_picker']))
                        @foreach($makeFilters['date_picker'] as $field => $date)
                            <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4">
                                <x-livewire-powergrid::filters.date-picker
                                    :date="$date"
                                    :inline="false"
                                    classAttr="w-full"
                                    :theme="$theme->filterDatePicker"/>
                            </div>
                        @endforeach
                    @endif

                    @if(isset($makeFilters['select']))
                        @foreach($makeFilters['select'] as $field => $select)
                            <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4">
                                <x-livewire-powergrid::filters.select
                                    :select="$select"
                                    :inline="false"
                                    :theme="$theme->filterSelect"/>
                            </div>
                        @endforeach
                    @endif

                    @if(isset($makeFilters['number']))
                        @foreach($makeFilters['number'] as $field => $number)
                            <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4">
                                <x-livewire-powergrid::filters.number
                                    :number="$number"
                                    :inline="false"
                                    :theme="$theme->filterNumber"/>
                            </div>
                        @endforeach
                    @endif

                    @if(isset($makeFilters['select']))
                        @foreach($makeFilters['select'] as $field => $select)
                            <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4">
                                <x-livewire-powergrid::filters.select
                                    :select="$select"
                                    :inline="false"
                                    :theme="$theme->filterSelect"/>
                            </div>
                        @endforeach
                    @endif
                    @if(isset($makeFilters['number']))
                        @foreach($makeFilters['number'] as $field => $number)
                            <div class="flex flex-col mb-2 md:w-1/2 lg:w-1/4">
                                <x-livewire-powergrid::filters.number
                                    :number="$number"
                                    :inline="false"
                                    :theme="$theme->filterNumber"/>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
