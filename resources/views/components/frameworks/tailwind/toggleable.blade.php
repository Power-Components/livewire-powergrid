@php
    $showDefaultToggle = false;
    if (str_contains($primaryKey, '.')) {
        $showDefaultToggle = true;
    }

    $value = (int) $row->{$column->field};

    $trueValue = $column->toggleable['default'][0];
    $falseValue = $column->toggleable['default'][1];

    $params = [
        'id' => data_get($row, $primaryKey),
        'isHidden' => !$showToggleable ? 'true' : 'false',
        'tableName' => $tableName,
        'field' => $column->field,
        'toggle' => $value,
        'trueValue' => $trueValue,
        'falseValue' => $falseValue,
    ];
@endphp
<div x-data="pgToggleable(@js($params))">
    @if ($column->toggleable['enabled'] && !$showDefaultToggle && $showToggleable === true)
        <div class="flex">
            <div
                :class="{
                    'relative rounded-full w-8 h-4 transition duration-200 ease-linear': true,
                    'bg-pg-secondary-600 dark:pg-secondary-500': toggle,
                    'bg-pg-primary-200': !toggle
                }">
                <label
                    :class="{
                        'absolute left-0 bg-white border-2 mb-2 w-4 h-4 rounded-full transition transform duration-100 ease-linear cursor-pointer': true,
                        'translate-x-full border-pg-secondary-600': toggle,
                        'translate-x-0 border-pg-primary-200': !toggle
                    }"
                    x-on:click="save"
                ></label>
                <input
                    type="checkbox"
                    class="appearance-none opacity-0 w-full h-full active:outline-none focus:outline-none"
                    x-on:click="save"
                >
            </div>
        </div>
    @else
        <div class="flex flex-row justify-center">
            <div @class([
                'text-xs px-4 w-auto py-1 text-center rounded-md',
                'bg-red-200 text-red-800' => $value === 0,
                'bg-blue-200 text-blue-800' => $value === 1,
            ])>
                {{ $value === 0 ? $falseValue : $trueValue }}
            </div>
        </div>
    @endif
</div>
