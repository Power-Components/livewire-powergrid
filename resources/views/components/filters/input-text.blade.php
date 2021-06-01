@props([
    'theme' => '',
    'column' => null,
    'inline' => null,
    'inputText' => null
])
<div>
    @if(filled($inputText))
        <div class="@if($inline) pr-6 @endif pt-2 p-2">
            @if(!$inline)
                <label>{{ $inputText['label'] }}</label>
            @endif
            <div class="@if($inline) flex flex-col @else flex flex-row @endif">
                <div class="mt-1 mb-1 mr-2 ml-2 @if(!$inline) pr-4 @endif">
                    <div class="relative">
                        <select
                            id="input_text_options" class="{{ $theme->selectClass }}"
                            wire:input.debounce.800ms="filterInputTextOptions('{{ $inputText['field'] }}', $event.target.value)"
                        >
                            <option value="contains">{{ trans('livewire-powergrid::datatable.input_text_options.contains') }}</option>
                            <option value="contains_not">{{ trans('livewire-powergrid::datatable.input_text_options.contains_not') }}</option>
                            <option value="is">{{ trans('livewire-powergrid::datatable.input_text_options.is') }}</option>
                            <option value="is_not">{{ trans('livewire-powergrid::datatable.input_text_options.is_not') }}</option>
                            <option value="starts_with">{{ trans('livewire-powergrid::datatable.input_text_options.starts_with') }}</option>
                            <option value="ends_with">{{ trans('livewire-powergrid::datatable.input_text_options.ends_with') }}</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <x-livewire-powergrid::icons.down/>
                        </div>
                    </div>
                    <input
                        data-id="{{ $inputText['field'] }}"
                        wire:model.lazy="filters_enabled.{{ $column->field }}"
                        wire:input.lazy="filterInputText('{{ $inputText['field'] }}', $event.target.value)"
                        type="text"
                        class="{{ $theme->inputClass }}"
                        placeholder="{{ $column->title }}">
                </div>
            </div>
        </div>
    @endif
</div>
