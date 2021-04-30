@if(filled($number))
    <div>
        @if(!$inline)
            <label>{{ $number['label'] }}</label>
        @endif
            <div class="@if($inline) flex flex-col @else flex flex-row @endif">
                <div class="mt-1 mb-1 @if(!$inline) pr-4 @endif">
                   <div class="relative">
                        <select
                        id="input_text_options" class="appearance-none livewire_powergrid_input flatpickr flatpickr-input block appearance-no mt-1 mb-1 bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 roundedleading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active w-9/12'"

                        wire:input.debounce.800ms="filterInputTextOptions('{{ $number['field'] }}', $event.target.value)"
                        >
                         <option value="contains">{{ trans('livewire-powergrid::datatable.input_text_options.contains') }}</option>

                         <option value="contains_not">{{ trans('livewire-powergrid::datatable.input_text_options.contains_not') }}</option>

                         <option value="is">{{ trans('livewire-powergrid::datatable.input_text_options.is') }}</option>

                          <option value="is_not">{{ trans('livewire-powergrid::datatable.input_text_options.is_not') }}</option>

                          <option value="starts_with">{{ trans('livewire-powergrid::datatable.input_text_options.starts_with') }}</option>

                          <option value="ends_with">{{ trans('livewire-powergrid::datatable.input_text_options.ends_with') }}</option>

                        </select>
                    </div>
                    <input
                        data-id="{{ $number['field'] }}"
                        wire:model.debounce.800ms="filters_enabled.{{ $column->field }}"
                        wire:input.debounce.800ms="filterInputText('{{ $number['field'] }}', $event.target.value)"
                        type="text"
                        class="w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        placeholder="{{ $column->title }}">
                </div>
            </div>
    </div>
@endif

