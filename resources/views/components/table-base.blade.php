@props([
    'theme' => null,
    'readyToLoad' => false,
])
<div @isset($this->setUp['responsive']) x-data="pgResponsive" @endisset>
    <table
        class="table power-grid-table {{ $theme->tableClass }}"
        style="{{ $theme->tableStyle }}"
    >
        <thead
            class="{{ $theme->theadClass }}"
            style="{{ $theme->theadStyle }}"
        >
            {{ $header }}
        </thead>
        @if ($readyToLoad)
            <tbody
                class="{{ $theme->tbodyClass }}"
                style="{{ $theme->tbodyStyle }}"
            >
                {{ $body }}
            </tbody>
        @else
            <tbody
                class="{{ $theme->tbodyClass }}"
                style="{{ $theme->tbodyStyle }}"
            >
                {{ $loading }}
            </tbody>
        @endif
    </table>
</div>
