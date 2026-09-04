@props([
    'display' => '',
    'full' => '',
    'position' => 'top',
])

<span
    x-data="{ pgTip: false }"
    class="relative inline-flex cursor-help"
    @mouseenter="pgTip = true"
    @mouseleave="pgTip = false"
    @focusin="pgTip = true"
    @focusout="pgTip = false"
    tabindex="0"
    title="{{ $full }}"
>
    {!! $display !!}

    <span
        x-show="pgTip"
        x-cloak
        role="tooltip"
        @class([
            'absolute z-50 whitespace-normal break-words rounded-md bg-zinc-800 px-2 py-1 text-xs text-white shadow-lg max-w-xs w-max dark:bg-zinc-700',
            'bottom-full left-1/2 -translate-x-1/2 mb-1' => $position === 'top',
            'top-full left-1/2 -translate-x-1/2 mt-1' => $position === 'bottom',
            'right-full top-1/2 -translate-y-1/2 mr-1' => $position === 'left',
            'left-full top-1/2 -translate-y-1/2 ml-1' => $position === 'right',
        ])
    >{{ $full }}</span>
</span>
