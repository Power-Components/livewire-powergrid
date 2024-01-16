@props([
    'theme' => null,
    'readyToLoad' => false,
    'items' => null,
    'lazy' => false,
    'tableName' => null,
])
<div @isset($this->setUp['responsive']) x-data="pgResponsive" @endisset>
    <table
        id="table_base_{{ $tableName }}"
        class="table power-grid-table {{ data_get($theme, 'table.tableClass') }}"
        style="{{  data_get($theme, 'tableStyle') }}"
    >
        <thead
            class="{{ data_get($theme, 'table.theadClass') }}"
            style="{{ data_get($theme, 'table.theadStyle') }}"
        >
            {{ $header }}
        </thead>
        @if ($readyToLoad)
            <tbody
                class="{{  data_get($theme, 'table.tbodyClass') }}"
                style="{{  data_get($theme, 'table.tbodyStyle') }}"
            >
                {{ $body }}
            </tbody>
        @else
            <tbody
                class="{{  data_get($theme, 'table.tbodyClass') }}"
                style="{{  data_get($theme, 'table.tbodyStyle') }}"
            >
                {{ $loading }}
            </tbody>
        @endif
    </table>

    {{-- infinite pagination handler --}}
    @if ($this->canLoadMore && $lazy)
        <div class="justify-center items-center" wire:loading.class="flex" wire:target="loadMore">
            @include(powerGridThemeRoot() . '.header.loading')
        </div>

        <div x-data="pgLoadMore"></div>
    @endif
</div>
