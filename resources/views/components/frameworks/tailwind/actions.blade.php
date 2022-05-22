<div class="md:flex md:flex-row w-full">
    <div>
        <x-livewire-powergrid::actions-header
            :theme="$theme"
            :actions="$this->headers"/>
    </div>
    <div class="flex flex-row justify-center items-center text-sm">
        @if(count($exportOptions) > 0)
            <div class="mr-2 mt-2 sm:mt-0">
                @include(powerGridThemeRoot().'.export')
            </div>
        @endif
        @includeIf(powerGridThemeRoot().'.toggle-columns')
    </div>

    <!-- LOADING -->
    @include(powerGridThemeRoot().'.loading')
</div>
