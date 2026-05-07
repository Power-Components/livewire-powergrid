<div class="pg-header-container">
    @includeIf(data_get($setUp, 'header.includeViewOnTop'))
    <header class="pg-header {{ theme('header.layout.container') }}">
        <div class="pg-header-sub {{ theme('header.layout.sub_container') }}">
            <div class="pg-actions {{ theme('header.layout.actions_container') }}">
                 @includeIf(theme_view('header.actions'))
                 @includeIf(theme_view('header.export'))
                 @includeIf(theme_view('header.toggle-columns'))
            </div>

            <div class="pg-search {{ theme('header.search_box.container') }}">
                @includeIf(theme_view('header.search'))
            </div>
        </div>
    </header>
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'))
</div>
