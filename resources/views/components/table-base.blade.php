@props([
    'theme' => null,
    'readyToLoad' => false,
])
<div>
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
                {{ $rows }}
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
