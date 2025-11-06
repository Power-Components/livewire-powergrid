@php use PowerComponents\LivewirePowerGrid\Providers\SupportLivewireVersions; @endphp

@if (SupportLivewireVersions::isV4())
    @script
        <script>
            this.$js('pgRowTemplates', (rowTemplates) => {
                window['pgRowTemplates_' + $wire.id] = JSON.parse(rowTemplates);
            })
            this.$js('pgResourceIcons', (icons) => {
                window.pgResourceIcons = JSON.parse(icons);
            })
            this.$js('pgActions', (actions) => {
                window['pgActions_' + $wire.id] = JSON.parse(actions);
            })
            this.$js('pgActionsHeader', (actions) => {
                window['pgActionsHeader_' + $wire.id] = JSON.parse(actions);
            })
        </script>
    @endscript
@endif
