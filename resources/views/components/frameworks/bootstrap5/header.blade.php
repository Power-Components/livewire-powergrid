<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))
    <div class="dt--top-section">
        <div class="row">
            <div class="col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center">
                @include(powerGridThemeRoot() . '.header.actions')

                <div class="me-1">
                    @includeWhen(data_get($setUp, 'exportable'), powerGridThemeRoot() . '.header.export')
                </div>

                @include(powerGridThemeRoot() . '.header.toggle-columns')
                @includeIf(powerGridThemeRoot() . '.header.soft-deletes')

                @includeWhen(boolval(data_get($setUp, 'header.wireLoading')),
                    powerGridThemeRoot() . '.header.loading')
            </div>
            <div class="col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3">
                @include(powerGridThemeRoot() . '.header.search')
            </div>
        </div>
    </div>
    @include(powerGridThemeRoot() . '.header.batch-exporting')
    @include(powerGridThemeRoot() . '.header.enabled-filters')
    @include(powerGridThemeRoot() . '.header.multi-sort')
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
    @includeIf(powerGridThemeRoot() . '.header.message-soft-deletes')
</div>
