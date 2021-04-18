<div x-data="dropdown()" x-init="loadOptions()">
    <input name="values" type="hidden" x-bind:value="selectedValues()">
    <div class="inline-block relative w-full p-2" style="min-width: 180px !important;">
        <div class="flex flex-col items-center relative">
            <div x-on:click="open" class="w-full svelte-1l8159u multi_select_{{ $column->field }}"
                 style="min-width: 160px !important;">
                <div class="my-2 p-1 flex svelte-1l8159u">
                    <div class="flex flex-auto flex-wrap">
                        <template x-for="(option,index) in selected" :key="options[option].value">
                            <div
                                class="flex justify-center items-center m-1 font-medium py-1 px-2 bg-white rounded-full text-teal-700 bg-teal-100 border border-teal-300 ">
                                <div class="pl-0 align-middle cursor-pointer hover:text-black hover:text-current  text-left text-xs font-medium text-gray-700 uppercase tracking-wider x-model=
                                     options[option]" x-text="options[option].text"></div>
                                <div class="flex flex-auto flex-row-reverse">
                                    <div x-on:click="remove(index,option)">
                                        <svg class="fill-current h-5 w-6" role="button" viewBox="0 0 20 20">
                                            <path class="text-gray-500" d="M14.348,14.849c-0.469,0.469-1.229,0.469-1.697,0L10,11.819l-2.651,3.029c-0.469,0.469-1.229,0.469-1.697,0
                                           c-0.469-0.469-0.469-1.229,0-1.697l2.758-3.15L5.651,6.849c-0.469-0.469-0.469-1.228,0-1.697s1.228-0.469,1.697,0L10,8.183
                                           l2.651-3.031c0.469-0.469,1.228-0.469,1.697,0s0.469,1.229,0,1.697l-2.758,3.152l2.758,3.15
                                           C14.817,13.62,14.817,14.38,14.348,14.849z"/>
                                        </svg>

                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="selected.length == 0" class="flex-1">
                            <input placeholder="{{ trans('livewire-powergrid::datatable.placeholders.multi_select') }}"
                                   class="w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                   x-bind:value="selectedValues()"
                            >
                        </div>
                    </div>
                    <div
                        class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-gray-200 svelte-1l8159u">

                        <button type="button" x-show="isOpen() === true" x-on:click="open"
                                class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                            <svg version="1.1" class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                <path d="M17.418,6.109c0.272-0.268,0.709-0.268,0.979,0s0.271,0.701,0,0.969l-7.908,7.83
	c-0.27,0.268-0.707,0.268-0.979,0l-7.908-7.83c-0.27-0.268-0.27-0.701,0-0.969c0.271-0.268,0.709-0.268,0.979,0L10,13.25
	L17.418,6.109z"/>
                            </svg>

                        </button>
                        <button type="button" x-show="isOpen() === false" @click="close"
                                class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                            <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                <path d="M2.582,13.891c-0.272,0.268-0.709,0.268-0.979,0s-0.271-0.701,0-0.969l7.908-7.83
	c0.27-0.268,0.707-0.268,0.979,0l7.908,7.83c0.27,0.268,0.27,0.701,0,0.969c-0.271,0.268-0.709,0.268-0.978,0L10,6.75L2.582,13.891z
	"/>
                            </svg>

                        </button>
                    </div>
                </div>
            </div>
            <div class="w-full px-4">
                <div x-show.transition.origin.top="isOpen()"
                     class="absolute shadow top-100 bg-white z-40 w-full lef-0 rounded max-h-select overflow-y-auto svelte-5uyqqj"
                     x-on:click.away="close">
                    <div class="flex flex-col w-full">
                        <template x-for="(option,index) in options" :key="option">
                            <div>
                                <div class="cursor-pointer w-full border-gray-100 rounded-t border-b hover:bg-teal-100"
                                     @click="select(index,$event)">
                                    <div x-bind:class="option.selected ? 'border-teal-600' : ''"
                                         class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                                        <div class="w-full items-center flex">
                                            <div
                                                class=" pl-0 align-middle cursor-pointer hover:text-black hover:text-current text-left text-xs font-medium text-gray-600 uppercase tracking-wider leading-6"
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
<style>
    [x-cloak] {
        display: none;
    }
</style>
@push('powergrid_scripts')
    <!-- Power Grid Multi Select Scripts -->
    <script>
        function dropdown() {
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
                select(index, event) {

                    if (!this.options[index].selected) {

                        this.options[index].selected = true;
                        this.options[index].element = event.target;
                        this.selected.push(index);
                        window.livewire.emit('eventMultiSelect', {
                            id: '{!! $select['relation_id'] !!}',
                            values: this.selectedValues()
                        });
                        console.log(index)
                    } else {
                        this.selected.splice(this.selected.lastIndexOf(index), 1);
                        this.options[index].selected = false
                    }
                },
                remove(index, option) {
                    this.options[option].selected = false;
                    this.selected.splice(index, 1);
                    window.livewire.emit('eventMultiSelect', {
                        id: '{!! $select['relation_id'] !!}',
                        values: this.selectedValues()
                    });
                },
                loadOptions() {
                    const options = document.getElementById('select').options;
                    for (let i = 0; i < options.length; i++) {
                        this.options.push({
                            value: options[i].value,
                            text: options[i].innerText,
                            selected: options[i].getAttribute('selected') != null ? options[i].getAttribute('selected') : false
                        });
                    }
                },
                selectedValues() {
                    return this.selected.map((option) => {
                        return this.options[option].value;
                    })
                }
            }
        }
    </script>
    <!-- Power Grid Date Picker Scripts -->
@endpush
<label for="select" class="hidden"></label>
<select class="hidden" x-cloak id="select" id="input_{!! $select['relation_id'] !!}"
        wire:model="filters.select.{!! $select['relation_id'] !!}"
        wire:ignore>
    <option value="">{{ __('Todos') }}</option>
    @foreach($select['data_source'] as $relation)
        <option value="{{ $relation['id'] }}">{{ $relation[$select['display_field']] }}</option>
    @endforeach
</select>
