<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))
    <div class="dt--top-section">
        <div class="row">
            <div class="col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center">
                <div x-data="pgRenderActions">
                    <span class="{{ theme_style($theme, 'table.layout.actions') }}" x-html="toHtml"></span>
                </div>

                <div class="me-1">
                    @includeWhen(data_get($setUp, 'exportable'), data_get($theme, 'root') . '.header.export')
                </div>

                @include(data_get($theme, 'root') . '.header.toggle-columns')
                @includeIf(data_get($theme, 'root') . '.header.soft-deletes')

                @if (config('livewire-powergrid.filter') === 'outside')
                    @include(data_get($theme, 'root') . '.header.filters')
                @endif

                @includeWhen(boolval(data_get($setUp, 'header.wireLoading')),
                    data_get($theme, 'root') . '.header.loading')
            </div>
            <div class="col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3">
                @include(data_get($theme, 'root') . '.header.search')
            </div>
        </div>
    </div>
    @includeWhen(data_get($setUp, 'exportable.batchExport.queues', 0), data_get($theme, 'root') . '.header.batch-exporting')
    @includeIf(data_get($theme, 'root') . '.header.enabled-filters')
    @includeWhen($multiSort, data_get($theme, 'root') . '.header.multi-sort')
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
    @includeIf(data_get($theme, 'root') . '.header.message-soft-deletes')
</div>
