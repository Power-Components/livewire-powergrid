@props([
    'theme' => '',
    'inline' => null,
    'multiSelect' => null,
    'column' => null
])
<div wire:ignore x-cloak>
    <div x-data="dropdown('{{ $column->field }}', '{{ $multiSelect['data_field'] }}')"
         x-init="loadOptions()"
         x-cloak>
        <input name="values" type="hidden" readonly x-bind:value="selectedValues()">
        <div class="inline-block relative w-full p-2" style="min-width: 180px !important;">
            <div class="flex flex-col items-center relative">
                <div x-on:click="open" class="w-full multi_select_{{ $column->field }}"
                     style="min-width: 160px !important;">
                    <div
                        class="my-2 border focus:bg-white focus:border-gray-500 border-gray-300 dark:border-gray-500 rounded flex">
                        <div class="flex flex-auto flex-wrap">
                            <template x-for="(option,index) in selected" :key="options[option].value">
                                <div
                                    class="flex justify-center items-center m-1 font-medium py-1 px-2 bg-white dark:bg-gray-500 rounded-full text-teal-700 bg-teal-100 border border-teal-300 dark:border-gray-400">
                                    <div class="pl-0 align-middle cursor-pointer hover:text-black hover:text-current dark:text-gray-300 text-left text-xs font-medium text-gray-700 tracking-wider x-model=
                                     options[option]" x-text="options[option].text"></div>
                                    <div class="flex flex-auto flex-row-reverse">
                                        <div x-on:click="remove(index,option)">
                                            <x-livewire-powergrid::icons.x class="w-4 h-4 cursor-pointer"/>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="selected.length === 0" class="flex-1">
                                <input readonly
                                       name="multi_select_{{ $column->field }}"
                                       wire:model.debounce.500ms="filters.multi_select.{{ $column->field }}"
                                       placeholder="{{ trans($column->placeholder) ?: trans('livewire-powergrid::datatable.multi_select.select') }}"
                                       class="w-full block bg-white-200 text-gray-700 py-2 text-sm px-3 leading-tight focus:outline-none dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-400"
                                       x-bind:value="selectedValues()"
                                >
                            </div>
                        </div>
                        <div
                            class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-gray-200 dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-400">

                            <button type="button" x-show="isOpen() === true" x-on:click="open"
                                    class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none dark:text-gray-200">
                                <x-livewire-powergrid::icons.up
                                    class="w-4 h-4 text-gray-400 dark:text-gray-200 cursor-pointer"/>
                            </button>
                            <button type="button" x-show="isOpen() === false" @click="close"
                                    class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none dark:text-gray-200">
                                <x-livewire-powergrid::icons.down
                                    class="w-4 h-4 text-gray-400 dark:text-gray-200 cursor-pointer"/>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="w-full px-4">
                    <div x-show="isOpen()"
                         x-cloak
                         x-transition.origin.top
                         class="absolute z-100 shadow top-100 border border-gray-300 bg-white z-50 w-full lef-0 rounded max-h-select overflow-y-auto dark:bg-gray-700 dark:border-gray-400"
                         x-on:click.away="close">
                        <div class="flex flex-col w-full">
                            <template x-for="(option,index) in options" :key="index">
                                <div>
                                    <div
                                        class="cursor-pointer w-full border-gray-100 rounded-t border-b dark:hover:bg-gray-500 dark:hover:text-gray-300 dark:border-gray-400"
                                        @click="select(index,$event)">
                                        <div x-bind:class="option.selected ? 'border-teal-600' : ''"
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

    <!-- Power Grid Multi Select Scripts -->
    <script>
        function dropdown(field, data_field) {
            return {
                options: [],
                selected: [],
                show: false,
                open() {
                    this.show = true
                },
                close() {
                    this.show = false
                },
                isOpen() {
                    return this.show === true
                },
                selectedValues() {
                    return this.selected.map((option) => {
                        return this.options[option].value;
                    })
                },
                select(index, event) {

                    if (!this.options[index].selected) {

                        this.options[index].selected = true;
                        this.options[index].element = event.target;
                        this.selected.push(index);
                        this.show = false
                        window.livewire.emit('eventMultiSelect', {
                            id: data_field,
                            values: this.selectedValues()
                        });

                    } else {
                        this.selected.splice(this.selected.lastIndexOf(index), 1);
                        this.options[index].selected = false
                        this.show = false
                    }

                },
                remove(index, option) {
                    this.options[option].selected = false;
                    this.selected.splice(index, 1);
                    window.livewire.emit('eventMultiSelect', {
                        id: data_field,
                        values: this.selectedValues()
                    });
                },
                loadOptions() {
                    const options = document.getElementById('select_' + field).options;
                    for (let i = 0; i < options.length; i++) {
                        this.options.push({
                            value: options[i].value,
                            text: options[i].innerText,
                            selected: options[i].getAttribute('selected') != null ? options[i].getAttribute('selected') : false
                        });
                    }
                }
            }
        }
    </script>
    <!-- Power Grid Date Picker Scripts -->

    <label for="select" class="hidden"></label>
    <label for="select_{!! $column->field !!}"></label>
    <select class="hidden"
            x-cloak
            id="select_{!! $column->field !!}"
            wire:model.lazy="filters.multi_select.{!! $multiSelect['data_field'] !!}">
        <option value="">{{ trans('livewire-powergrid::datatable.multi_select.all') }}</option>
        @foreach($multiSelect['data_source'] as $relation)
            <option value="{{ $relation['id'] }}">{{ $relation[$multiSelect['display_field']] }}</option>
        @endforeach
    </select>

</div>
