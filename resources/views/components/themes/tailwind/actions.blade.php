<div class="md:flex md:flex-row w-full">
    <div class="flex flex-row justify-center items-center text-sm">
        @if (count($exportOptions) > 0)
            <div class="mr-2 mt-2 sm:mt-0">
                @include(theme_view('header.export'))
            </div>
        @endif
        @includeIf(theme_view('header.toggle-columns'))
    </div>

    <!-- LOADING -->
    @include(theme_view('header.loading'))
</div>
