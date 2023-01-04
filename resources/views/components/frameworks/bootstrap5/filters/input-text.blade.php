@props([
    'theme' => '',
    'enabledFilters' => [],
    'column' => null,
    'inline' => null,
    'filter' => null,
])
<div>
    @php
        $field = strval(data_get($filter, 'field'));
        $title = strval(data_get($filter, 'title'));

        $inputTextOptions = \PowerComponents\LivewirePowerGrid\Filters\FilterInputText::getInputTextOperators();

        $inputTextOptions  = filled(data_get($filter, 'operators', [])) ?
                                data_get($filter, 'operators') :
                                $inputTextOptions;

        $showSelectOptions = !(count($inputTextOptions) === 1 && in_array('contains', $inputTextOptions));
    @endphp
    @if(filled($filter))
        <div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
            @if($showSelectOptions)
            <div class="relative">
                <select class="power_grid {{ $theme->selectClass }} {{ data_get($column, 'headerClass') }}"
                        style="{{ data_get($column, 'headerStyle') }}"
                        wire:model.lazy="filters.input_text_options.{{ $field }}"
                        wire:input.lazy="filterInputTextOptions('{{ $field }}', $event.target.value)">
                    @foreach($inputTextOptions as $key => $value)
                        <option wire:key="input-text-options-{{ $tableName }}-{{ $key.'-'.$value }}" value="{{ $key }}">{{ trans('livewire-powergrid::datatable.input_text_options.'.$value) }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="mt-1">
                <input
                    data-id="{{ $field }}"
                    @if(isset($enabledFilters[$field]['disabled']) && boolval($enabledFilters[$field]['disabled']) === true) disabled @else
                        wire:model.debounce.700ms="filters.input_text.{{ $field  }}"
                        wire:input.debounce.700ms="filterInputText('{{ $field }}', $event.target.value)"
                    @endif
                    type="text"
                    class="power_grid {{ $theme->inputClass }}"
                    placeholder="{{ empty($column)?$title:($column->placeholder?:$column->title) }}" />
            </div>
        </div>
    @endif
</div>
