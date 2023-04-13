<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))
    <div class="md:flex md:flex-row w-full justify-between items-center">
        <div class="md:flex md:flex-row w-full">
            <div>
                @include(powerGridThemeRoot().'.header.actions')
            </div>
            <div class="flex flex-row justify-center items-center text-sm flex-wrap" x-cloak>
                @if(data_get($setUp, 'exportable'))
                    <div class="mr-2 mt-2 sm:mt-0" id="pg-header-export">
                        @include(powerGridThemeRoot().'.header.export')
                    </div>
                @endif
                @includeIf(powerGridThemeRoot().'.header.toggle-columns')
                @includeIf(powerGridThemeRoot().'.header.soft-deletes')
                    @if(config('livewire-powergrid.filter') == 'outside' && count($this->filters()) > 0)
                        @includeIf(powerGridThemeRoot().'.header.filters')
                    @endif
                @includeIf(powerGridThemeRoot().'.header.enabled-filters')
            </div>
            @includeWhen(boolval(data_get($setUp, 'header.wireLoading')), powerGridThemeRoot().'.header.loading')
        </div>
        @include(powerGridThemeRoot().'.header.search')
    </div>

    @include(powerGridThemeRoot().'.header.batch-exporting')
    @include(powerGridThemeRoot().'.header.multi-sort')
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
    @includeIf(powerGridThemeRoot().'.header.message-soft-deletes')
</div>


