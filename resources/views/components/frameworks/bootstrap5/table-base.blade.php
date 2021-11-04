<div>
    <div class="col-md-12">
        @include($theme->layout->header, [
                'enabledFilters' => $enabledFilters
        ])

        @include($theme->layout->message)

    </div>
    <div class="table-responsive col-md-12">
        @include($table)
    </div>
    <div class="col-md-12">
        @include($theme->footer->view, ['theme' => $theme])
    </div>
</div>


