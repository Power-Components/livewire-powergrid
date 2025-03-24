@php
    $value = (int) $row->{data_get($column, 'field')};
    $trueValue = data_get($column, 'toggleable')['default'][0];
    $falseValue = data_get($column, 'toggleable')['default'][1];
@endphp

<div class="flex flex-row justify-center">
    @if ($showToggleable)
        @php
            $params = [
                'id' => data_get($row, $this->realPrimaryKey),
                'isHidden' => !$showToggleable,
                'tableName' => $tableName,
                'field' => data_get($column, 'field'),
                'toggle' => $value,
                'trueValue' => $trueValue,
                'falseValue' => $falseValue,
            ];
        @endphp
        <input
            x-data="pgToggleable(@js($params))"
            type="checkbox"
            class="toggle toggle-sm"
            :checked="toggle"
            x-on:click="save"
        />
    @else
        <div @class([
            'text-xs px-4 w-auto py-1 text-center rounded-md',
            'bg-error text-error-content' => $value === 0,
            'bg-info text-info-content' => $value === 1,
        ])>
            {{ $value === 0 ? $falseValue : $trueValue }}
        </div>
    @endif
</div>
