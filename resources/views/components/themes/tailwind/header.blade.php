<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))

    <div class="{{ theme('header.layout.container') }}">
        <div class="{{ theme('header.layout.sub_container') }}">
            <div class="{{ theme('header.layout.actions_container') }}">
                {!! $this->renderHeaderActions() !!}
                {!! $this->renderPluginZone('header') !!}
                @includeIf(theme_view('header.toggle-columns'))
                @includeIf(theme_view('header.soft-deletes'))
                @if ($this->usesFilterPanel() && count($this->filters()) > 0 && ! $this->filterBuilderHidesDefaultFilters())
                    @includeIf(theme_view('header.filters'))
                @endif
            </div>
            @includeWhen(boolval(data_get($setUp, 'header.wireLoading')),
                theme_view('header.loading'))
        </div>
        @include(theme_view('header.search'))
    </div>

    @includeIf(theme_view('header.enabled-filters'))

    {!! $this->renderPluginZone('header.bottom') !!}

    @includeWhen($multiSort, theme_view('header.multi-sort'))
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
    @includeIf(theme_view('header.message-soft-deletes'))
</div>
