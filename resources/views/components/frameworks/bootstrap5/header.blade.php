<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))
    <div class="dt--top-section">
        <div class="row">
            <div class="col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center">
                @include(data_get($this->theme, 'root') . '.header.actions')

                <div class="me-1">
                    @includeWhen(data_get($setUp, 'exportable'), data_get($this->theme, 'root') . '.header.export')
                </div>

                @include(data_get($this->theme, 'root') . '.header.toggle-columns')
                @includeIf(data_get($this->theme, 'root') . '.header.soft-deletes')

                @includeWhen(boolval(data_get($setUp, 'header.wireLoading')),
                    data_get($this->theme, 'root') . '.header.loading')
            </div>
            <div class="col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3">
                @include(data_get($this->theme, 'root') . '.header.search')
            </div>
        </div>
    </div>
    @include(data_get($this->theme, 'root') . '.header.batch-exporting')
    @include(data_get($this->theme, 'root') . '.header.enabled-filters')
    @include(data_get($this->theme, 'root') . '.header.multi-sort')
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
    @includeIf(data_get($this->theme, 'root') . '.header.message-soft-deletes')
</div>
