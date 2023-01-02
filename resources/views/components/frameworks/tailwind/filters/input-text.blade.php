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
        <div @class([
            'p-2' => !$inline,
            $theme->baseClass,
        ]) style="{{ $theme->baseStyle }}">
            @if(!$inline)
                <label class="text-gray-700 dark:text-gray-300">{{ $title }}</label>
            @endif
            <div @class([
                'sm:flex w-full' => !$inline,
                'flex flex-col' => $inline,
                ])>
                @if($showSelectOptions)
                    <div @class([
                            'pl-0 pt-1 w-full sm:pr-3 sm:w-1/2' => !$inline,
                        ])>
                        <div class="relative">
                            <select class="power_grid {{ $theme->selectClass }} {{ data_get($column, 'headerClass') }}"
                                    style="{{ data_get($column, 'headerStyle') }}"
                                    wire:model.lazy="filters.input_text_options.{{ $field }}"
                                    wire:input.lazy="filterInputTextOptions('{{ $field }}', $event.target.value)">
                                @foreach($inputTextOptions as $key => $value)
                                    <option wire:key="input-text-options-{{ $tableName }}-{{ $key.'-'.$value }}" value="{{ $value }}">{{ trans('livewire-powergrid::datatable.input_text_options.'.$value) }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-pg-primary-700">
                                <x-livewire-powergrid::icons.down class="w-4 h-4 dark:text-gray-300"/>
                            </div>
                        </div>
                    </div>
                @endif
                <div @class([
                        'pl-0 pt-1 w-full sm:w-1/2' => !$inline,
                        'mt-1' => $inline,
                    ])>
                    <input
                        data-id="{{ $field }}"
                        @if(isset($enabledFilters[$field]['disabled']) && boolval($enabledFilters[$field]['disabled']) === true) disabled @else
                        wire:model.debounce.700ms="filters.input_text.{{ $field  }}"
                        wire:input.debounce.700ms="filterInputText('{{ $field }}', $event.target.value, '{{ $title }}')"
                        @endif
                        type="text"
                        class="power_grid {{ $theme->inputClass }}"
                        placeholder="{{ empty($column) ? $title:($column->placeholder?:$column->title) }}" />
                </div>
            </div>
        </div>
    @endif
</div>
