<div class="md:flex md:flex-row w-full">
    <div>
        <x-livewire-powergrid::frameworks.tailwind.header.actions
            :theme="$theme"
            :actions="$this->headers"
        />
    </div>
    <div class="flex flex-row justify-center items-center text-sm">
        @if (count($exportOptions) > 0)
            <div class="mr-2 mt-2 sm:mt-0">
                @include(data_get($theme, 'root') . '.export')
            </div>
        @endif
        @includeIf(data_get($theme, 'root') . '.toggle-columns')
    </div>

    <!-- LOADING -->
    @include(data_get($theme, 'root') . '.loading')
</div>
