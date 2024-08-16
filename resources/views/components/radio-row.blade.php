@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => data_get($theme, 'radio.inputClass'),
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
        class="{{ data_get($theme, 'radio.tdClass') }}"
        style="{{ data_get($theme, 'radio.tdStyle') }}"
    >
    </td>
@elseif($disable)
    <td
        class="{{ data_get($theme, 'radio.tdClass') }}"
        style="{{ data_get($theme, 'radio.tdStyle') }}"
    >
        <div class="{{ data_get($theme, 'radio.divClass') }}">
            <label class="{{ data_get($theme, 'radio.labelClass') }}">
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
        class="{{ data_get($theme, 'radio.thClass') }}"
        style="{{ data_get($theme, 'radio.thStyle') }}"
    >
        <div class="{{ data_get($theme, 'radio.divClass') }}">
            <label class="{{ data_get($theme, 'radio.labelClass') }}">
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
