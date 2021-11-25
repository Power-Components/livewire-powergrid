<div>
    <div class="col-md-12">
        @include($theme->layout->header, [
                'enabledFilters' => $enabledFilters
        ])

        @includeIf($theme->layout->message)

    </div>
    <div class="table-responsive col-md-12" style="margin: 10px 0 10px;">
        @include($table)
    </div>
    <div class="col-md-12">
        @include($theme->footer->view, ['theme' => $theme])
    </div>
</div>


