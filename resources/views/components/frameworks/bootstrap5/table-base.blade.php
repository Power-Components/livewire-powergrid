<div>
    <div class="col-md-12">
        @include($theme->layout->header, [
                'enabledFilters' => $enabledFilters
        ])

        @if(config('livewire-powergrid.filter') === 'outside')
            @if(count($makeFilters) > 0)
                <div>
                    <x-livewire-powergrid::frameworks.bootstrap5.filter
                        :makeFilters="$makeFilters"
                        :theme="$theme"
                    />
                </div>
            @endif
        @endif

        @include($theme->layout->message)

    </div>
    <div class="table-responsive col-md-12">
        @include($table)
    </div>
    <div class="col-md-12">
        @include($theme->footer->view, ['theme' => $theme])
    </div>
</div>


