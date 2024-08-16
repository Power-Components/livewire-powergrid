@php
    $params = [
        'id' => data_get($row, $this->realPrimaryKey),
        'isHidden' => !$showToggleable,
        'tableName' => $tableName,
        'field' => data_get($column, 'field'),
        'toggle' => (int) $row->{data_get($column, 'field')},
        'trueValue' => data_get($column, 'toggleable')['default'][0],
        'falseValue' => data_get($column, 'toggleable')['default'][1],
    ];
@endphp

<div x-data="pgToggleable(@js($params))">
    @if (data_get($column, 'toggleable')['enabled'] && $showToggleable === true)
        <div class="form-check form-switch">
            <label>
                <input
                    x-on:click="save()"
                    class="form-check-input"
                    :checked="toggle === 1"
                    type="checkbox"
                >
            </label>
        </div>
    @else
        <div class="text-center">
            @if ($row->{data_get($column, 'field')} == 0)
                <div
                    x-text="falseValue"
                    style="padding-top: 0.1em; padding-bottom: 0.1rem"
                    class="badge bg-danger"
                >
                </div>
            @else
                <div
                    x-text="trueValue"
                    style="padding-top: 0.1em; padding-bottom: 0.1rem"
                    class="badge bg-success"
                >
                </div>
            @endif
        </div>
    @endif
</div>
