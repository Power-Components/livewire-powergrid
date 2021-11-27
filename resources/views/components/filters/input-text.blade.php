@props([
    'theme' => '',
    'enabledFilters' => [],
    'column' => null,
    'inline' => null,
    'inputText' => null
])
<div>
    @php
        $field = data_get($inputText, 'dataField') ?? data_get($inputText, 'field');
    @endphp
    @if(filled($inputText))
        <div class="@if(!$inline) pt-2 p-2 @endif">
            @if(!$inline)
                <label class="text-gray-700 dark:text-gray-300">{{ data_get($inputText, 'label') }}</label>
            @endif
            <div class="@if($inline) flex flex-col @else flex flex-row justify-between @endif">
                <div class="@if(!$inline) pl-0 pt-1 pr-3 @endif">
                    <div class="relative">
                        <select id="input_text_options"
                                class="power_grid {{ $theme->selectClass }} {{ data_get($column, 'headerClass') }}"
                                style="{{ data_get($column, 'headerStyle') }}"
                                wire:model.debounce.800ms="filters.input_option_text.{{ $field }}"
                                wire:input.debounce.300ms="filterInputTextOptions('{{ $field }}', $event.target.value, '{{ data_get($inputText, 'label') }}')">
                            <option
                                value="contains">{{ trans('livewire-powergrid::datatable.input_text_options.contains') }}</option>
                            <option
                                value="contains_not">{{ trans('livewire-powergrid::datatable.input_text_options.contains_not') }}</option>
                            <option
                                value="is">{{ trans('livewire-powergrid::datatable.input_text_options.is') }}</option>
                            <option
                                value="is_not">{{ trans('livewire-powergrid::datatable.input_text_options.is_not') }}</option>
                            <option
                                value="starts_with">{{ trans('livewire-powergrid::datatable.input_text_options.starts_with') }}</option>
                            <option
                                value="ends_with">{{ trans('livewire-powergrid::datatable.input_text_options.ends_with') }}</option>
                            <option
                                value="is_null">{{ trans('livewire-powergrid::datatable.input_text_options.is_null') }}</option>
                            <option
                                value="is_not_null">{{ trans('livewire-powergrid::datatable.input_text_options.is_not_null') }}</option>
                            <option
                                value="is_blank">{{ trans('livewire-powergrid::datatable.input_text_options.is_blank') }}</option>
                            <option
                                value="is_not_blank">{{ trans('livewire-powergrid::datatable.input_text_options.is_not_blank') }}</option>
                            <option
                                value="is_empty">{{ trans('livewire-powergrid::datatable.input_text_options.is_empty') }}</option>
                            <option
                                value="is_not_empty">{{ trans('livewire-powergrid::datatable.input_text_options.is_not_empty') }}</option>
                        </select>
                        <div class="{{ $theme->relativeDivClass }}">
                            <x-livewire-powergrid::icons.down class="w-4 h-4"/>
                        </div>
                    </div>
                </div>
                <div class="mt-1">
                    <input
                        data-id="{{ $field }}"
                        @if(isset($enabledFilters[$field]['disabled']) && boolval($enabledFilters[$field]['disabled']) === true) disabled @else
                        wire:model.debounce.800ms="filters.input_text.{{ $field  }}"
                        wire:input.debounce.800ms="filterInputText('{{ $field }}', $event.target.value, '{{ data_get($inputText, 'label') }}')"
                        @endif
                        type="text"
                        class="power_grid {{ $theme->inputClass }}"
                        placeholder="{{ empty($column)?data_get($inputText, 'label'):($column->placeholder?:$column->title) }}">
                </div>
            </div>
        </div>
    @endif
</div>
