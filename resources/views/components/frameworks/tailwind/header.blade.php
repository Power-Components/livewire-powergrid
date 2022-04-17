<div>
    @includeIf(data_get($setUp, 'footer.includeViewOnTop'))
    <div class="md:flex md:flex-row w-full justify-between items-center">
        <div class="md:flex md:flex-row w-full">
            <div>
                <x-livewire-powergrid::actions-header
                    :theme="$theme"
                    :actions="$headers"/>
            </div>
            <div class="flex flex-row justify-center items-center text-sm">
                @if(data_get($setUp, 'exportable'))
                    <div class="mr-2 mt-2 sm:mt-0">
                        @include(powerGridThemeRoot().'.header.export')
                    </div>
                @endif
                @includeIf(powerGridThemeRoot().'.header.toggle-columns')
            </div>
            @include(powerGridThemeRoot().'.header.loading')
        </div>
        @include(powerGridThemeRoot().'.header.search')
    </div>

    @include(powerGridThemeRoot().'.header.batch-exporting')
    @include(powerGridThemeRoot().'.header.enabled-filters')

    @includeIf(data_get($setUp, 'footer.includeViewOnBottom'))
</div>


