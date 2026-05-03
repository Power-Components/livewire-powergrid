@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => theme('radio.input'),
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
        class="{{ theme('radio.td') }}"
    >
    </td>
@elseif($disable)
    <td
        class="{{ theme('radio.td') }}"
    >
        <div class="{{ theme('radio.base') }}">
            <label class="{{ theme('radio.label') }}">
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
        class="{{ theme('radio.th') }}"
    >
        <div class="{{ theme('radio.base') }}">
            <label class="{{ theme('radio.label') }}">
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
