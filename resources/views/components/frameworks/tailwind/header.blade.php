<div>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))
    <div class="mb-3 md:flex md:flex-row w-full justify-between items-center">
        <div class="md:flex md:flex-row w-full">
            <div>
                @include(data_get($theme, 'root') . '.header.actions')
            </div>
            <div class="flex flex-row items-center text-sm flex-wrap">
                @if (data_get($setUp, 'exportable'))
                    <div
                        class="mr-2 mt-2 sm:mt-0"
                        id="pg-header-export"
                    >
                        @include(data_get($theme, 'root') . '.header.export')
                    </div>
                @endif
                @includeIf(data_get($theme, 'root') . '.header.toggle-columns')
                @includeIf(data_get($theme, 'root') . '.header.soft-deletes')
                @if (config('livewire-powergrid.filter') == 'outside' && count($this->filters()) > 0)
                    @includeIf(data_get($theme, 'root') . '.header.filters')
                @endif
            </div>
            @includeWhen(boolval(data_get($setUp, 'header.wireLoading')),
                data_get($theme, 'root') . '.header.loading')
        </div>
        @include(data_get($theme, 'root') . '.header.search')
    </div>

    @includeIf(data_get($theme, 'root') . '.header.enabled-filters')

    @include(data_get($theme, 'root') . '.header.batch-exporting')
    @include(data_get($theme, 'root') . '.header.multi-sort')
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
    @includeIf(data_get($theme, 'root') . '.header.message-soft-deletes')
</div>
