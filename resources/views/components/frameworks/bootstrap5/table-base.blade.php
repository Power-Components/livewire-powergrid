<div>
    <div class="col-md-12">
        @include($theme->layout->header, [
                'filtersEnabled' => $filters_enabled
        ])
    </div>
    <div class="table-responsive col-md-12">
        @include($table)
    </div>
    <div class="col-md-12">
        @include($theme->footer->view, ['theme' => $theme])
    </div>
</div>


