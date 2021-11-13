<div class="md:flex md:flex-row w-full justify-between">

    <div class="md:flex md:flex-row w-full">

        <div>
            <x-livewire-powergrid::actions-header
                :theme="$theme"
                :actions="$this->headers"/>
        </div>

        <div class="flex flex-row">

            @if($exportOption)
                <div class="mr-2 mt-2 sm:mt-0">
                    @include(powerGridThemeRoot().'.export')
                </div>
            @endif

            @includeIf(powerGridThemeRoot().'.toggle-columns')

        </div>

        @includeIf(!$batchExporting, powerGridThemeRoot().'.loading')

    </div>

    @include(powerGridThemeRoot().'.search')

</div>

@include(powerGridThemeRoot().'.batch-exporting')

@include(powerGridThemeRoot().'.enabled-filters')

