@props([
    'theme' => null,
    'readyToLoad' => false,
    'items' => null,
])
<div @isset($this->setUp['responsive']) x-data="pgResponsive" @endisset>
    <table
        class="table power-grid-table {{ data_get($theme, 'tableClass') }}"
        style="{{  data_get($theme, 'tableStyle') }}"
    >
        <thead
            class="{{  data_get($theme, 'theadClass') }}"
            style="{{  data_get($theme, 'theadStyle') }}"
        >
            {{ $header }}
        </thead>
        @if ($readyToLoad)
            <tbody
                class="{{  data_get($theme, 'tbodyClass') }}"
                style="{{  data_get($theme, 'tbodyStyle') }}"
            >
                {{ $body }}
            </tbody>
        @else
            <tbody
                class="{{  data_get($theme, 'tbodyClass') }}"
                style="{{  data_get($theme, 'tbodyStyle') }}"
            >
                {{ $loading }}
            </tbody>
        @endif
    </table>

    {{-- loading indicator --}}
    <div class="flex justify-center items-center" wire:loading target="loadMore">
        @include(powerGridThemeRoot() . '.header.loading')
    </div>

    {{-- infinite pagination handler --}}
    @if ($this->canLoadMore)
        <div x-init="() => {
            const observer = new IntersectionObserver(entries => {
                if (entries[0].isIntersecting) {
                      $wire.call('loadMore')
                }
            })

            observer.observe($el)
        }"></div>
    @endif
</div>
