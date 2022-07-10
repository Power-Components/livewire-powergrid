@props([
    'theme' => '',
    'inline' => null,
    'multiSelect' => null,
    'column' => null,
    'tableName' => null,
    'selected' => [],
])
@php
    $data = collect($multiSelect['data_source'])
         ->transform(function (array|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model $entry) use ($multiSelect) {
             if (is_array($entry)) {
                 $entry = collect($entry);
             }
             return [
                 'value' => $entry->only([
                     $multiSelect['value'],
                     $multiSelect['text']
                 ])
             ];
         });

    $pluralField = \Illuminate\Support\Str::plural($multiSelect['dataField']);

    if (isset($selected[$multiSelect['dataField']])) {
        $selected = collect($selected[$multiSelect['dataField']])
                ->map(fn ($value) => intval($value))
                ->toArray();
    } else {
        $selected  = collect(request()->get($tableName.'_'.$pluralField))
        ->map(fn ($value) => intval($value))
        ->toArray();
    }


@endphp
<div x-cloak
     wire:ignore.self
     x-data="pgMultiSelect({
        data: @js($data),
        value: '{{ $multiSelect['value'] }}',
        text: '{{ $multiSelect['text'] }}',
        tableName: '{{ $tableName }}',
        dataField: '{{ $multiSelect['dataField'] }}',
        selected: @js($selected)
     })">
    <div @class([
            'p-2' => !$inline,
            $theme->baseClass,
        ]) style="{{ $theme->baseStyle }}">
        @if(!$inline)
            <label class="text-gray-700 dark:text-gray-300">{{ data_get($multiSelect, 'label') }}</label>
        @endif

        <div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
            <div class="flex flex-col items-center relative">
                <div x-on:click="show = true" class="w-full"
                     style="min-width: 160px !important;">
                    <div
                        class="my-1 border focus:bg-white focus:border-slate-500 border-slate-300 dark:border-slate-500 rounded flex">
                        <div class="flex flex-auto flex-wrap">
                            <div x-cloak
                                 x-show="selected.length > 0"
                                 x-text="selected.length"
                                 class="text-sm text-slate-500 flex p-1 dark:text-slate-200 items-center justify-center"></div>
                            <template x-for="(option, index) in selected" :key="index">
                                <div
                                    class="flex justify-center items-center m-1 cursor-pointer inline-flex rounded-full items-center py-0.5 pl-2.5 pr-1 text-sm font-medium bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-900">
                                    <div
                                        class="pl-0 align-middle cursor-pointer hover:text-black hover:text-current dark:text-slate-300 text-left text-xs font-medium text-slate-700 tracking-wider"
                                        x-text="option.text"></div>

                                    <button type="button"
                                            x-on:click="remove(option.value)"
                                            class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-200 hover:text-slate-500 focus:outline-none focus:bg-slate-500 focus:text-slate-200 dark:hover:bg-slate-300 dark:hover:text-slate-500">
                                        <span class="sr-only"></span>
                                        <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                            <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
                                        </svg>
                                    </button>

                                </div>
                            </template>
                            <div x-show="selected.length === 0" class="flex-1">
                                <input readonly
                                       x-ref="multi_select"
                                       placeholder="{{ $column ? trans($column->placeholder) ?: trans('livewire-powergrid::datatable.multi_select.select') : '' }}"
                                       class="w-full block bg-white text-slate-700 py-2 text-sm px-3 leading-tight focus:outline-none dark:bg-slate-600 dark:text-slate-200 dark:placeholder-slate-200 dark:border-slate-500"
                                       x-bind:value="selectedValues()"
                                >
                            </div>
                        </div>
                        <div
                            class="text-slate-300 w-8 py-1 pl-2 pr-1 border-l flex bg-white items-center border-slate-200
                                    dark:bg-slate-600 dark:text-slate-200 dark:placeholder-slate-200 dark:border-slate-500">

                            <button type="button" x-show="show" x-on:click="show = true"
                                    class="cursor-pointer w-6 h-6 text-slate-600 outline-none focus:outline-none dark:text-slate-200">
                                <x-livewire-powergrid::icons.up
                                    class="w-4 h-4 text-slate-400 dark:text-slate-200 cursor-pointer"/>
                            </button>
                            <button type="button" x-show="!show" @click="show = false"
                                    class="cursor-pointer w-6 h-6 text-slate-600 outline-none focus:outline-none dark:text-slate-200">
                                <x-livewire-powergrid::icons.down
                                    class="w-4 h-4 text-slate-400 dark:text-slate-200 cursor-pointer"/>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="w-full px-4">
                    <div x-show="show"
                         x-cloak
                         x-transition.origin.top
                         class="absolute rounded-lg overflow-hidden z-100 shadow-md top-100 border border-slate-300
                                bg-white z-50 w-full left-0 rounded max-h-select overflow-y-auto dark:bg-slate-700
                                dark:border-slate-500"
                         x-on:click.outside="show = false">
                        <div class="flex flex-col w-full">
                            <template x-for="(option,index) in options" :key="index">
                                <div>
                                    <div
                                        x-show="option.selected === false"
                                        class="relative group cursor-pointer hover:bg-slate-50 w-full border-slate-100 rounded-t border-b
                                                dark:hover:bg-slate-500 dark:hover:text-slate-300 dark:border-slate-500"
                                        x-on:click="select(option.value)">
                                        <div x-bind:class="option.selected ? 'border-slate-600' : ''"
                                             class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                                            <div class="w-full items-center flex">
                                                <div
                                                    class="dark:text-slate-200 pl-0 align-middle cursor-pointer hover:text-black hover:text-current
                                                           text-left text-xs font-medium text-slate-600 tracking-wider leading-6"
                                                    x-model="option" x-text="option.text"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        x-show="option.selected === true"
                                        x-on:click="remove(option.value)"
                                        class="relative group cursor-pointer hover:bg-slate-50 w-full border-slate-100 rounded-t border-b
                                               dark:hover:bg-slate-500 dark:bg-slate-600 dark:hover:text-slate-300 dark:border-slate-500">
                                        <div x-bind:class="option.selected ? 'border-slate-600' : ''"
                                             class="flex w-full items-center p-2 bg-slate-200 dark:bg-slate-600 dark:border-slate-500 border-transparent relative">
                                            <div class="w-full items-center flex">
                                                <div
                                                    class="dark:text-slate-200 pl-0 align-middle cursor-pointer hover:text-black hover:text-current text-left text-xs font-medium text-slate-600 tracking-wider leading-6"
                                                    x-model="option" x-text="option.text"></div>
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                                    <svg class="w-5 h-5 text-slate-500 dark:text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
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
</div>
