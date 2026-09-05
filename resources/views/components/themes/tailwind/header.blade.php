@php
    $__partial = $__partial ?? $this;
    $setUp = $setUp ?? $__partial->setUp;
    $tableName = $tableName ?? $__partial->tableName;
    $multiSort = $multiSort ?? $__partial->multiSort;
@endphp
<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'), ['__partial' => $__partial])

    <div class="{{ theme('header.layout.container') }}">
        {{-- Left: user custom buttons / plugins --}}
        <div class="{{ theme('header.layout.actions_container') }}">
            {!! $__partial->renderHeaderActions() !!}
            @includeIf(theme_view('header.soft-deletes'), ['__partial' => $__partial])
            @includeWhen(boolval(data_get($setUp, 'header.wireLoading')),
                theme_view('header.loading'), ['__partial' => $__partial])
        </div>

        {{-- Right: built-in controls grouped next to the search box.
             Order: columns / export & plugins / filter — filter sits glued to search. --}}
        <div class="{{ theme('header.layout.sub_container') }} flex flex-row flex-wrap items-center gap-2 w-full mt-2 md:mt-0 md:max-w-xl {{ $__partial->headerControlsAlignClass() }}">
            @includeIf(theme_view('header.toggle-columns'), ['__partial' => $__partial])
            {!! $__partial->renderPluginZone('header') !!}
            <div class="relative flex flex-1 min-w-0 flex-row items-center gap-2">
                {!! $__partial->renderPluginZone('header.filter') !!}
                @if ($__partial->usesFilterFlyout() && count($__partial->declaredFilters()) > 0 && ! $__partial->filterBuilderHidesDefaultFilters())
                    @includeIf(theme_view('header.filters'), ['__partial' => $__partial])
                @endif
                @if ($__partial->usesFilterDropdown() && count($__partial->declaredFilters()) > 0 && ! $__partial->filterBuilderHidesDefaultFilters())
                    @include(theme_view($__partial->filterPanelView()), [
                        '__partial' => $__partial,
                        'tableName' => $tableName,
                    ])
                @endif
                <div class="flex-1 min-w-0">
                    @include(theme_view('header.search'), ['__partial' => $__partial])
                </div>
            </div>
        </div>
    </div>

    @includeIf(theme_view('header.enabled-filters'), ['__partial' => $__partial])

    @php($headerBottomZone = $__partial->renderPluginZone('header.bottom'))
    @if (trim($headerBottomZone) !== '')
        <div class="px-4 pb-3">
            {!! $headerBottomZone !!}
        </div>
    @endif

    @includeWhen($multiSort, theme_view('header.multi-sort'), ['__partial' => $__partial])
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'), ['__partial' => $__partial])
    @includeIf(theme_view('header.message-soft-deletes'), ['__partial' => $__partial])
</div>
