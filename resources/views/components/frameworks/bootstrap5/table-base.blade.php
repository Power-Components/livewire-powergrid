<div @if ($deferLoading) wire:init="fetchDatasource" @endif>
    <div class="col-md-12">
        @include($theme->layout->header, [
            'enabledFilters' => $enabledFilters,
        ])
    </div>
    <div
        class="{{ $theme->table->divClass }}"
        style="{{ $theme->table->divStyle }}"
    >
        @include($table)
    </div>
    <div class="row">
        <div class="col-12 overflow-auto">
            @include($theme->footer->view, ['theme' => $theme])
        </div>
    </div>
</div>
