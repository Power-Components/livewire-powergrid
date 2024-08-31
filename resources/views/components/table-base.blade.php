@props([
    'readyToLoad' => false,
    'items' => null,
    'lazy' => false,
    'tableName' => null,
    'theme' => null,
])
<div @isset($this->setUp['responsive']) x-data="pgResponsive" @endisset>
    <table
        id="table_base_{{ $tableName }}"
        class="table power-grid-table {{ theme_style($theme, 'table.layout.table') }}"
    >
        <thead
            class="{{ theme_style($theme, 'table.header.thead') }}"
        >
            {{ $header }}
        </thead>
        @if ($readyToLoad)
            <tbody
                class="{{ theme_style($theme, 'table.body.tbody') }}"
            >
                {{ $body }}
            </tbody>
        @else
            <tbody
                class="{{ theme_style($theme, 'table.body.tbody') }}"
            >
                {{ $loading }}
            </tbody>
        @endif
    </table>

    {{-- infinite pagination handler --}}
    @if ($this->canLoadMore && $lazy)
        <div class="justify-center items-center" wire:loading.class="flex" wire:target="loadMore">
            @include(data_get($theme, 'root') . '.header.loading')
        </div>

        <div x-data="pgLoadMore"></div>
    @endif
</div>
