<div class="md:flex md:flex-row w-full">
    <div class="flex flex-row justify-center items-center text-sm">
        {!! $this->renderPluginZone('header') !!}
        @includeIf(theme_view('header.toggle-columns'))
    </div>

    <!-- LOADING -->
    @include(theme_view('header.loading'))
</div>
