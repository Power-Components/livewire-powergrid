@props([
    'enabledFilters' => [],
    'column' => null,
    'inline' => null,
    'filter' => null,
])
<div>
    @php
        $fieldClassName = data_get($filter, 'className');

        $field = strval(data_get($filter, 'field'));
        $title = strval(data_get($column, 'title'));
        $operators = (array) data_get($filter, 'operators', []);
        $placeholder = strval(data_get($filter, 'placeholder'));
        $componentAttributes = (array) data_get($filter, 'attributes', []);

        $inputTextOptions = $fieldClassName::getInputTextOperators();
        $inputTextOptions = count($operators) > 0 ? $operators : $inputTextOptions;
        $showSelectOptions = !(count($inputTextOptions) === 1 && in_array('contains', $inputTextOptions));

        $defaultPlaceholder = data_get($column, 'placeholder') ?: data_get($column, 'title');
        $overridePlaceholder = $placeholder ?: $defaultPlaceholder;

        unset($filter['placeholder']);

        $defaultAttributes = $fieldClassName::getWireAttributes($field, $title);

        $selectClasses = theme_style($theme, 'filterInputText.select');
        $inputClasses = theme_style($theme, 'filterInputText.input');

        $params = array_merge(
            [
                'showSelectOptions' => $showSelectOptions,
                'placeholder' => ($placeholder = $componentAttributes['placeholder'] ?? $overridePlaceholder),
                ...data_get($filter, 'attributes'),
                ...$defaultAttributes,
            ],
            $filter,
        );
    @endphp

    @if ($params['component'])
        @unset($params['operators'], $params['attributes'])

        <x-dynamic-component
            :component="$params['component']"
            :attributes="new \Illuminate\View\ComponentAttributeBag($params)"
        />
    @else
        <div class="{{ theme_style($theme, 'filterInputText.base') }}">
            @if (!$inline)
                <label class="form-label fw-semibold mb-1">{{ $title }}</label>
            @endif
            <div class="d-flex flex-row gap-2 align-items-center">
                @if ($showSelectOptions)
                    <select
                        class="form-select form-select-sm w-auto"
                        style="{{ data_get($column, 'headerStyle') }}"
                        {{ $defaultAttributes['selectAttributes'] }}
                    >
                        @foreach ($inputTextOptions as $key => $value)
                            <option
                                wire:key="input-text-options-{{ $tableName }}-{{ $key . '-' . $value }}"
                                value="{{ $value }}"
                            >{{ trans('livewire-powergrid::datatable.input_text_options.' . $value) }}</option>
                        @endforeach
                    </select>
                @endif
                <input
                    wire:key="input-{{ $field }}"
                    data-id="{{ $field }}"
                    @if (isset($enabledFilters[$field]['disabled']) && boolval($enabledFilters[$field]['disabled']) === true) disabled
                        @else
                            {{ $defaultAttributes['inputAttributes'] }} @endif
                    type="text"
                    class="form-control form-control-sm"
                    placeholder="{{ $placeholder }}"
                />
            </div>
        </div>
    @endif
</div>
