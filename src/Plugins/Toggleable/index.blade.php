@props([
    'row' => null,
    'column' => null,
    'tableName' => null,
    'primaryKey' => null,
    'showToggleable' => true,
    'js' => null,
])

@php
    $value = (int) $row->{data_get($column, 'field')};

    $trueValue = data_get($column, 'pluginData.toggleable')['default'][0];
    $falseValue = data_get($column, 'pluginData.toggleable')['default'][1];

    // Colors come entirely from the active theme's `toggleable` tokens (light and
    // dark), so nothing theme-specific lives in shared CSS. Each value is a CSS
    // color with a hard fallback, and dark is applied by the colorless var-swap
    // emitted once below.
    $colorOn = theme('toggleable.color_on', 'var(--color-accent, #16a34a)');
    $colorOff = theme('toggleable.color_off', 'var(--color-zinc-200, #e4e4e7)');
    $colorOnDark = theme('toggleable.color_on_dark', $colorOn);
    $colorOffDark = theme('toggleable.color_off_dark', $colorOff);
    $knobOn = theme('toggleable.knob_on', 'var(--color-accent-foreground, #ffffff)');

    $switchVars = "--pg-toggle-on-light: {$colorOn};"
        ." --pg-toggle-off-light: {$colorOff};"
        ." --pg-toggle-on-dark: {$colorOnDark};"
        ." --pg-toggle-off-dark: {$colorOffDark};"
        ." --pg-toggle-knob-on: {$knobOn};";
@endphp

@once
<script>
    {!! $js !!}
</script>
<style>
    /* Colorless mechanism: map the active on/off vars from the per-element source
       vars (set inline from the theme tokens), swapping to the dark set under .dark. */
    .pg-toggleable-switch {
        --pg-toggle-on: var(--pg-toggle-on-light);
        --pg-toggle-off: var(--pg-toggle-off-light);
        background-color: var(--pg-toggle-off);
    }
    .pg-toggleable-switch.pg-toggleable-on {
        background-color: var(--pg-toggle-on);
    }
    .pg-toggleable-knob {
        transform: translateX(0);
        background-color: #fff;
        /* Shadow + hairline ring keep the knob defined on any track color. */
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(0, 0, 0, 0.12);
        transition: transform 0.1s ease-linear, background-color 0.15s ease-linear;
    }
    .pg-toggleable-on .pg-toggleable-knob {
        transform: translateX(100%);
        /* Use the accent's foreground color so the knob contrasts with the "on"
           track — critical when the accent itself is white (Flux dark mode). */
        background-color: var(--pg-toggle-knob-on);
    }
    .dark .pg-toggleable-switch {
        --pg-toggle-on: var(--pg-toggle-on-dark);
        --pg-toggle-off: var(--pg-toggle-off-dark);
    }
</style>
@endonce

<div class="flex flex-row justify-center">
    @if ($showToggleable)
        @php
            $params = [
                'id' => data_get($row, $primaryKey),
                'isHidden' => !$showToggleable,
                'tableName' => $tableName,
                'field' => data_get($column, 'field'),
                'toggle' => $value,
                'trueValue' => $trueValue,
                'falseValue' => $falseValue,
            ];
        @endphp
        <div
            x-data="pgToggleable"
            data-pg-params="{{ json_encode($params) }}"
            role="switch"
            tabindex="0"
            :aria-checked="ariaChecked()"
            :class="onClass()"
            class="pg-toggleable-switch relative inline-block w-8 h-4 rounded-full cursor-pointer transition-colors duration-200 ease-linear"
            style="{{ $switchVars }}"
            x-on:click="save()"
            x-on:keydown.enter.prevent="save()"
            x-on:keydown.space.prevent="save()"
        >
            <span
                class="pg-toggleable-knob absolute left-0 top-0 block w-4 h-4 rounded-full"
            ></span>
        </div>
    @else
        <div @class([
            'text-xs px-4 w-auto py-1 text-center rounded-md',
            'bg-red-200 text-red-800' => $value === 0,
            'bg-blue-200 text-blue-800' => $value === 1,
        ])>
            {{ $value === 0 ? $falseValue : $trueValue }}
        </div>
    @endif
</div>
