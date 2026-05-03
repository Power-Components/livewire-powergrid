<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))

    <div class="{{ theme('header.container') }}">
        <div class="{{ theme('header.sub_container') }}">
            <div x-data="pgRenderActions">
                <span class="pg-actions" x-html="toHtml"></span>
            </div>

            <div class="{{ theme('header.actions') }}">
                @if (data_get($setUp, 'exportable'))
                    <div
                        class="mt-2 sm:mt-0"
                        id="pg-header-export"
                    >
                        @include(theme_view('header.export'))
                    </div>
                @endif

                @includeIf(theme_view('header.toggle-columns'))
                @includeIf(theme_view('header.soft-deletes'))

                @if (config('livewire-powergrid.filter') == 'outside' && count($this->filters()) > 0)
                    @includeIf(theme_view('header.filters'))
                @endif
            </div>

            @includeWhen(boolval(data_get($setUp, 'header.wireLoading')), theme_view('header.loading'))
        </div>
        @include(theme_view('header.search'))
    </div>

    @includeIf(theme_view('header.enabled-filters'))

    @includeWhen(data_get($setUp, 'exportable.batchExport.queues', 0), theme_view('header.batch-exporting'))
    @includeWhen($multiSort, theme_view('header.multi-sort'))
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
    @includeIf(theme_view('header.message-soft-deletes'))
</div>
