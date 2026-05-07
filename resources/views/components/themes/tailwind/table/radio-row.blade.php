@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => theme('table.radio.input'),
    ]);

    $rules = collect($row->__powergrid_rules)
        ->where('apply', true)
        ->where('forAction', \PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager::TYPE_RADIO)
        ->last();

    if (isset($rules['attributes'])) {
        foreach ($rules['attributes'] as $key => $value) {
            $inputAttributes = $inputAttributes->merge([
                $key => $value,
            ]);
        }
    }

    $disable = (bool) data_get($rules, 'disable');
    $hide = (bool) data_get($rules, 'hide');
@endphp
@if ($hide)
    <td
        class="{{ theme('table.radio.td') }}"
    >
    </td>
@elseif($disable)
    <td
        class="{{ theme('table.radio.td') }}"
    >
        <div class="{{ theme('table.radio.base') }}">
            <label class="{{ theme('table.radio.label') }}">
                <input
                    {{ $inputAttributes }}
                    disabled
                    type="radio"
                >
            </label>
        </div>
    </td>
@else
    <td
        class="{{ theme('table.radio.th') }}"
    >
        <div class="{{ theme('table.radio.base') }}">
            <label class="{{ theme('table.radio.label') }}">
                <input
                    type="radio"
                    {{ $inputAttributes }}
                    wire:model.live="selectedRow"
                    value="{{ $attribute }}"
                >
            </label>
        </div>
    </td>
@endif
