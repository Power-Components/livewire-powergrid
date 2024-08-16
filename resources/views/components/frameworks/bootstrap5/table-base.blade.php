<div @if ($deferLoading) wire:init="fetchDatasource" @endif>
    <div class="col-md-12">
        @include(theme_style($this->theme, 'layout.header'), [
            'enabledFilters' => $enabledFilters,
        ])
    </div>
    <div
        class="{{ theme_style($this->theme, 'table.layout.div') }}"
        style="{{ theme_style($this->theme, 'table.layout.div.1') }}"
    >
        @include($table)
    </div>
    <div class="row">
        <div class="col-12 overflow-auto">
            @include(theme_style($this->theme, 'footer.view'))
        </div>
    </div>
</div>
