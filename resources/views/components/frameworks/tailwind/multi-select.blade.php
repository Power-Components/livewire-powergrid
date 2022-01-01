@props([
    'theme' => '',
    'inline' => null,
    'multiSelect' => null,
    'column' => null,
    'tableName' => null,
])
@php
    $data = collect($multiSelect['data_source'])->transform(function ($entry) {
        return [
            'value' => $entry
        ];
    })
@endphp
<div x-cloak
     x-data="pgMultiSelect({
            data: {{ json_encode($data) }},
            tableName: '{{ $tableName }}',
            dataField: '{{ $multiSelect['data_field'] }}',
         })">
    <div class="inline-block relative w-full p-2"
         style="min-width: 180px !important;">
        <div class="flex flex-col items-center relative">
            <div x-on:click="show = true" class="w-full"
                 style="min-width: 160px !important;">
                <div
                    class="my-2 border focus:bg-white focus:border-gray-500 border-gray-300 dark:border-gray-500 rounded flex">
                    <div class="flex flex-auto flex-wrap">
                        <template x-for="(option,index) in selected" :key="options[option].value">
                            <div
                                class="flex justify-center items-center m-1 font-medium p-1 bg-white dark:bg-gray-500 rounded-lg text-blue-700 bg-blue-100 border border-blue-300 dark:border-gray-400">
                                <div class="pl-0 align-middle cursor-pointer hover:text-black hover:text-current dark:text-gray-300 text-left text-xs font-medium text-gray-700 tracking-wider x-model=
                                     options[option]" x-text="options[option].text"></div>
                                <div class="flex flex-auto flex-row-reverse">
                                    <div x-on:click="remove(index,option)">
                                        <x-livewire-powergrid::icons.x class="h-5 w-5 dark:text-gray-300 cursor-pointer"/>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="selected.length === 0" class="flex-1">
                            <input readonly
                                   x-ref="multi_select"
                                   placeholder="{{ trans($column->placeholder) ?: trans('livewire-powergrid::datatable.multi_select.select') }}"
                                   class="w-full block bg-white-200 text-gray-700 py-2 text-sm px-3 leading-tight focus:outline-none dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-400"
                                   x-bind:value="selectedValues()"
                            >
                        </div>
                    </div>
                    <div
                        class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-gray-200 dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-400">

                        <button type="button" x-show="show" x-on:click="show = true"
                                class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none dark:text-gray-200">
                            <x-livewire-powergrid::icons.up
                                class="w-4 h-4 text-gray-400 dark:text-gray-200 cursor-pointer"/>
                        </button>
                        <button type="button" x-show="!show" @click="show = false"
                                class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none dark:text-gray-200">
                            <x-livewire-powergrid::icons.down
                                class="w-4 h-4 text-gray-400 dark:text-gray-200 cursor-pointer"/>
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-full px-4">
                <div x-show="show"
                     x-cloak
                     x-transition.origin.top
                     class="absolute z-100 shadow top-100 border border-gray-300 bg-white z-50 w-full left-0 rounded max-h-select overflow-y-auto dark:bg-gray-700 dark:border-gray-400"
                     x-on:click.away="show = false">
                    <div class="flex flex-col w-full">
                        <template x-for="(option,index) in options" :key="index">
                            <div>
                                <div
                                    class="cursor-pointer w-full border-gray-100 rounded-t border-b dark:hover:bg-gray-500 dark:hover:text-gray-300 dark:border-gray-400"
                                    @click="select(index,$event)">
                                    <div x-bind:class="option.selected ? 'border-blue-600' : ''"
                                         class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                                        <div class="w-full items-center flex">
                                            <div
                                                class="dark:text-gray-200 pl-0 align-middle cursor-pointer hover:text-black hover:text-current text-left text-xs font-medium text-gray-600 tracking-wider leading-6"
                                                x-model="option" x-text="option.text"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
