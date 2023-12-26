@props([
    'theme' => null,
    'readyToLoad' => false,
    'items' => null,
    'lazy' => false,
])
<div @isset($this->setUp['responsive']) x-data="pgResponsive" @endisset>
    <table
        class="table power-grid-table {{ data_get($theme, 'tableClass') }}"
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

    @if($lazy)
        {{-- loading indicator --}}
        <div class="justify-center items-center" wire:loading.class="flex" wire:target="loadMore">
            @include(powerGridThemeRoot() . '.header.loading')
        </div>

        {{-- infinite pagination handler --}}
        @if ($this->canLoadMore)
            <div x-init="() => {const observer = new IntersectionObserver(entries => {if (entries[0].isIntersecting) {$wire.call('loadMore')}}); observer.observe($el)}"></div>
        @endif
    @endif
</div>
