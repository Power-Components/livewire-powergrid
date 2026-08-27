<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))

    <div class="{{ theme('header.layout.container') }}">
        {{-- Left: user custom buttons / plugins --}}
        <div class="{{ theme('header.layout.actions_container') }}">
            {!! $this->renderHeaderActions() !!}
            @includeIf(theme_view('header.soft-deletes'))
            @includeWhen(boolval(data_get($setUp, 'header.wireLoading')),
                theme_view('header.loading'))
        </div>

        {{-- Right: built-in controls grouped next to the search box.
             Order: columns / export & plugins / filter — filter sits glued to search. --}}
        <div class="{{ theme('header.layout.sub_container') }} flex flex-row flex-wrap items-center gap-2 w-full mt-2 md:mt-0 md:flex-1 md:max-w-xl md:ml-auto md:justify-end">
            @includeIf(theme_view('header.toggle-columns'))
            {!! $this->renderPluginZone('header') !!}
            <div class="relative flex flex-1 min-w-0 flex-row items-center gap-2">
                {!! $this->renderPluginZone('header.filter') !!}
                @if ($this->usesFilterFlyout() && count($this->declaredFilters()) > 0 && ! $this->filterBuilderHidesDefaultFilters())
                    @includeIf(theme_view('header.filters'))
                @endif
                @if ($this->usesFilterDropdown() && count($this->declaredFilters()) > 0 && ! $this->filterBuilderHidesDefaultFilters())
                    @include(theme_view($this->filterPanelView()), [
                        '__partial' => $this,
                        'tableName' => $tableName,
                    ])
                @endif
                <div class="flex-1 min-w-0">
                    @include(theme_view('header.search'))
                </div>
            </div>
        </div>
    </div>

    @includeIf(theme_view('header.enabled-filters'))

    @php($headerBottomZone = $this->renderPluginZone('header.bottom'))
    @if (trim($headerBottomZone) !== '')
        <div class="px-4 pb-3">
            {!! $headerBottomZone !!}
        </div>
    @endif

    @includeWhen($multiSort, theme_view('header.multi-sort'))
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
    @includeIf(theme_view('header.message-soft-deletes'))
</div>
