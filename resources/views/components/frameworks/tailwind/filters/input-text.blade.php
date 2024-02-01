@props([
    'theme' => '',
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

        $selectClasses = \Illuminate\Support\Arr::toCssClasses(['power_grid', data_get($theme, 'selectClass'), data_get($column, 'headerClass')]);
        $inputClasses = \Illuminate\Support\Arr::toCssClasses(['power_grid', data_get($theme, 'inputClass')]);

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
        <div
            class="{{ data_get($theme, 'baseClass') }}"
            style="{{ data_get($theme, 'baseStyle') }}"
        >
            @if (!$inline)
                <label class="block text-sm font-medium text-pg-primary-700 dark:text-pg-primary-300">
                    {{ $title }}
                </label>
            @endif
            <div @class([
                'sm:flex w-full' => !$inline && $showSelectOptions,
                'flex flex-col' => $inline && $showSelectOptions,
            ])>
                @if ($showSelectOptions)
                    <div @class([
                        'pl-0 pt-1 w-full sm:pr-3 sm:w-1/2' => !$inline,
                    ])>
                        <div class="relative">
                            <select
                                class="{{ $selectClasses }}"
                                style="{{ data_get($column, 'headerStyle') }}"
                                data-cy="input_text_options_{{ $tableName }}_{{ $field }}"
                                {{ $defaultAttributes['selectAttributes'] }}
                            >
                                @foreach ($inputTextOptions as $key => $value)
                                    <option
                                        wire:key="input-text-options-{{ $tableName }}-{{ $key . '-' . $value }}"
                                        value="{{ $value }}"
                                    >{{ trans('livewire-powergrid::datatable.input_text_options.' . $value) }}</option>
                                @endforeach
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-pg-primary-700 dark:text-pg-primary-300">
                                <x-livewire-powergrid::icons.down class="w-4 h-4 dark:text-gray-300" />
                            </div>
                        </div>
                    </div>
                @endif
                <div @class([
                    'pl-0 pt-1 w-full sm:w-1/2' => !$inline && $showSelectOptions,
                    'mt-1' => $inline,
                    'pt-1' => !$showSelectOptions,
                ])>
                    <input
                        data-cy="input_text_{{ $tableName }}_{{ $field }}"
                        wire:key="input-{{ $field }}"
                        data-id="{{ $field }}"
                        @if (isset($enabledFilters[$field]['disabled']) && boolval($enabledFilters[$field]['disabled']) === true) disabled
                            @else
                                {{ $defaultAttributes['inputAttributes'] }} @endif
                        type="text"
                        class="{{ $inputClasses }}"
                        placeholder="{{ $placeholder }}"
                    />
                </div>
            </div>
        </div>
    @endif
</div>
