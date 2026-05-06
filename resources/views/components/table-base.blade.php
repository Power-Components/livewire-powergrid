@props([
    'readyToLoad' => false,
    'items' => null,
    'tableName' => null,
])
<div @isset($this->setUp['responsive']) x-data="pgResponsive" @endisset>
    <div x-data="{ expandedId: null }">
        <table
            id="table_base_{{ $tableName }}"
            class="table {{ theme('table.table') }}"
        >
            <thead
                class="{{ theme('table.thead') }}"
            >
                {{ $header }}
            </thead>
            @if ($readyToLoad)
                <tbody
                    class="{{ theme('table.tbody') }}"
                >
                    {{ $body }}
                </tbody>
            @else
                <tbody
                    class="{{ theme('table.tbody') }}"
                >
                    {{ $loading }}
                </tbody>
            @endif
        </table>
    </div>

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
</div>
