<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))
    <div class="md:flex md:flex-row w-full justify-between items-center">
        <div class="md:flex md:flex-row w-full">
            <div>
                @include(powerGridThemeRoot().'.header.actions')
            </div>
            <div class="flex flex-row justify-center items-center text-sm">
                @if(data_get($setUp, 'exportable'))
                    <div class="mr-2 mt-2 sm:mt-0">
                        @include(powerGridThemeRoot().'.header.export')
                    </div>
                @endif
                @includeIf(powerGridThemeRoot().'.header.toggle-columns')
                @includeIf(powerGridThemeRoot().'.header.soft-deletes')
            </div>
            @include(powerGridThemeRoot().'.header.loading')
        </div>
        @include(powerGridThemeRoot().'.header.search')
    </div>

    @include(powerGridThemeRoot().'.header.batch-exporting')
    @include(powerGridThemeRoot().'.header.enabled-filters')

    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
    @includeIf(powerGridThemeRoot().'.header.message-soft-deletes')
</div>


