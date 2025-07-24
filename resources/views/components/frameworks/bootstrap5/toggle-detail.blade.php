<td
    x-data="{ collapsed: false }"
    class="{{ theme_style($theme, 'table.body.td') }}"
>
    <div
        x-on:click="
            const isOpen = expandedId == '{{ $rowId }}';

            expandedId = isOpen ? null : '{{ $rowId }}';
            collapsed = !isOpen;
            $dispatch('toggle-detail-{{ $rowId }}', { 'collapsed' : collapsed });
            "
        x-on:powergrid-detail-loaded.window="loading = false;"
    >
        <div>
            @includeIf(data_get($setUp, 'detail.viewIcon'))
            @unless (data_get($setUp, 'detail.viewIcon'))
                <div
                    x-bind:class='{
                        "bs5-rotate-90": collapsed && expandedId === "{{ $rowId }}",
                        "bs5-rotate-0": !collapsed
                    }'>
                    <x-livewire-powergrid::icons.arrow
                        class="bs5-w-h-1_5em"
                        style="cursor:pointer"
                    />
                </div>
                @endif
            </div>
        </div>
    </td>
