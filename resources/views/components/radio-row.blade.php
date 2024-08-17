@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => theme_style($theme, 'radio.input'),
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
        class="{{ theme_style($theme, 'radio.td') }}"
        style="{{ theme_style($theme, 'radio.td.1') }}"
    >
    </td>
@elseif($disable)
    <td
        class="{{ theme_style($theme, 'radio.td') }}"
        style="{{ theme_style($theme, 'radio.td.1') }}"
    >
        <div class="{{ theme_style($theme, 'radio.base') }}" style="{{ theme_style($theme, 'radio.base.1') }}">
            <label class="{{ theme_style($theme, 'radio.label') }}">
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
        class="{{ theme_style($theme, 'radio.th') }}"
        style="{{ theme_style($theme, 'radio.th') }}"
    >
        <div class="{{ theme_style($theme, 'radio.base') }}" style="{{ theme_style($theme, 'radio.base.1') }}">
            <label class="{{ theme_style($theme, 'radio.label') }}" style="{{ theme_style($theme, 'radio.label.1') }}">
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
