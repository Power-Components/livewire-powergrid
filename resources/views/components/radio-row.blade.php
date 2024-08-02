@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => data_get($theme, 'radio.inputClass'),
    ]);

    //    if (filled($rulesValues['setAttributes'])) {
    //        foreach ($rulesValues['setAttributes'] as $rulesAttributes) {
    //            $inputAttributes = $inputAttributes->merge([
    //                $rulesAttributes['attribute'] => $rulesAttributes['value'],
    //            ]);
    //        }
    //    }

    $hide = (bool) data_get(
        collect($this->actionRulesForRows[$rowId])
            ->where('apply', true)
            ->last(),
        'hide',
    );

    $disable = (bool) data_get(
        collect($this->actionRulesForRows[$rowId])
            ->where('apply', true)
            ->last(),
        'disable',
    );
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
