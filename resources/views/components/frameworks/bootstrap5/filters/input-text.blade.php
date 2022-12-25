@props([
    'theme' => '',
    'enabledFilters' => [],
    'column' => null,
    'inline' => null,
    'inputText' => null,
    'inputTextOptions' => [],
])
<div>
    @php
        $field = data_get($inputText, 'dataField') ?? data_get($inputText, 'field');

        $inputTextOptions  = data_get($inputText, 'operators') ?? $inputTextOptions;
        $showSelectOptions = !(count($inputTextOptions) === 1 && in_array('contains', $inputTextOptions)) &&
            filled($inputTextOptions);
    @endphp
    @if(filled($inputText))
        <div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
            @if($showSelectOptions)
            <div class="relative">
                <select class="power_grid {{ $theme->selectClass }} {{ data_get($column, 'headerClass') }}"
                        style="{{ data_get($column, 'headerStyle') }}"
                        wire:input.lazy="filterInputTextOptions('{{ $field }}', $event.target.value)">
                    @foreach($inputTextOptions as $key => $value)
                        <option value="{{ $key }}">{{ trans('livewire-powergrid::datatable.input_text_options'.$value) }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="mt-1">
                <input
                    data-id="{{ $field }}"
                    @if(isset($enabledFilters[$field]['disabled']) && boolval($enabledFilters[$field]['disabled']) === true) disabled @else
                        wire:model.debounce.800ms="filters.input_text.{{ $field  }}"
                        wire:input.debounce.800ms="filterInputText('{{ $field }}', $event.target.value)"
                    @endif
                    type="text"
                    class="power_grid {{ $theme->inputClass }}"
                    placeholder="{{ empty($column)?data_get($inputText, 'label'):($column->placeholder?:$column->title) }}" />
            </div>
        </div>
    @endif
</div>
