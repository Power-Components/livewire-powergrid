<div @if ($deferLoading) wire:init="fetchDatasource" @endif>
    <div class="col-md-12">
        @include(data_get($theme, 'layout.header'), [
            'enabledFilters' => $enabledFilters,
        ])
    </div>
    <div
            class="{{ data_get($theme, 'table.divClass') }}"
            style="{{ data_get($theme, 'table.divStyle') }}"
    >
        @include($table)
    </div>

    @if (!data_get($setUp, 'footer.hideIfResultIsMoreThenTotal') ||
        $total > data_get($setUp, 'footer.defaultPerPage') ||
        $total > data_get($setUp, 'footer.perPage'))
        <div class="row">
            <div class="col-12 overflow-auto">
                @include(data_get($theme, 'footer.view'), ['theme' => $theme])
            </div>
        </div>
    @endif
</div>
