@props([
    'theme' => '',
    'column' => null,
    'inline' => null,
    'inputText' => null
])
<div>
    @if(filled($inputText))
        <div class="@if(!$inline) pt-2 p-2 @endif">
            @if(!$inline)
                <label>{{ $inputText['label'] }}</label>
            @endif
            <div class="@if($inline) flex flex-col @else flex flex-row @endif">
                <div class="@if(!$inline) pl-0 pt-1 pr-3 @endif">
                    <div class="relative">
                        <select id="input_text_options" class="power_grid {{ $theme->selectClass }}"
                                wire:input.lazy="filterInputTextOptions('{{ $inputText['field'] }}', $event.target.value, '{{ $inputText['label'] }}')">
                            <option value="contains">{{ trans('livewire-powergrid::datatable.input_text_options.contains') }}</option>
                            <option value="contains_not">{{ trans('livewire-powergrid::datatable.input_text_options.contains_not') }}</option>
                            <option value="is">{{ trans('livewire-powergrid::datatable.input_text_options.is') }}</option>
                            <option value="is_not">{{ trans('livewire-powergrid::datatable.input_text_options.is_not') }}</option>
                            <option value="starts_with">{{ trans('livewire-powergrid::datatable.input_text_options.starts_with') }}</option>
                            <option value="ends_with">{{ trans('livewire-powergrid::datatable.input_text_options.ends_with') }}</option>
                        </select>
                        <div class="{{ $theme->relativeDivClass }}">
                            <x-livewire-powergrid::icons.down/>
                        </div>
                    </div>
                    <input
                        data-id="{{ $inputText['field'] }}"
                        wire:input.lazy="filterInputText('{{ $inputText['field'] }}', $event.target.value, '{{ $inputText['label'] }}')"
                        type="text"
                        class="power_grid {{ $theme->inputClass }}"
                        placeholder="{{ $column->title }}">
                </div>
            </div>
        </div>
    @endif
</div>
