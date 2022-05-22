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
    @endphp
    @if(filled($inputText))
        <div @class([
            'p-2' => !$inline,
            $theme->baseClass,
        ]) style="{{ $theme->baseStyle }}">
            @if(!$inline)
                <label class="text-gray-700 dark:text-gray-300">{{ data_get($inputText, 'label') }}</label>
            @endif
            <div @class([
                'sm:flex w-full' => !$inline,
                'flex flex-col' => $inline,
                ])>
                <div @class([
                        'pl-0 pt-1 w-full sm:pr-3 sm:w-1/2' => !$inline,
                    ])>
                    <div class="relative">
                        <select class="power_grid {{ $theme->selectClass }} {{ data_get($column, 'headerClass') }}"
                                style="{{ data_get($column, 'headerStyle') }}"
                                wire:model.lazy="filters.input_text_options.{{ $field }}"
                                wire:input.lazy="filterInputTextOptions('{{ $field }}', $event.target.value, '{{ data_get($inputText, 'label') }}')">
                            @foreach($inputTextOptions as $key => $value)
                                <option value="{{ $key }}">{{ trans($value) }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700">
                            <x-livewire-powergrid::icons.down class="w-4 h-4 dark:text-gray-300"/>
                        </div>
                    </div>
                </div>
                <div @class([
                        'pl-0 pt-1 w-full sm:w-1/2' => !$inline,
                        'mt-1' => $inline,
                    ])>
                    <input
                        data-id="{{ $field }}"
                        @if(isset($enabledFilters[$field]['disabled']) && boolval($enabledFilters[$field]['disabled']) === true) disabled @else
                        wire:model.debounce.800ms="filters.input_text.{{ $field  }}"
                        wire:input.debounce.800ms="filterInputText('{{ $field }}', $event.target.value, '{{ data_get($inputText, 'label') }}')"
                        @endif
                        type="text"
                        class="power_grid {{ $theme->inputClass }}"
                        placeholder="{{ empty($column)?data_get($inputText, 'label'):($column->placeholder?:$column->title) }}" />
                </div>
            </div>
        </div>
    @endif
</div>
