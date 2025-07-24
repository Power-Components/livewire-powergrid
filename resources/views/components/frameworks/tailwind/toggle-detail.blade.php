<td
    x-data="{ collapsed: false, loading: false }"
    class="{{ theme_style($theme, 'table.body.td') }}"
>
    <div
        class="cursor-pointer flex items-center"
        x-on:click.prevent="
            loading = true;
            collapsed = !collapsed;
            $dispatch('toggle-detail-{{ $rowId }}', { 'collapsed' : collapsed });
        "
        x-on:powergrid-detail-loaded.window="loading = false;"
    >
        <div x-show="loading">
            <x-livewire-powergrid::icons.loading
                class="text-pg-primary-300 dark:text-pg-primary-400 h-5 w-5 animate-spin"
            />
        </div>

        <div x-show="!loading">
            @includeIf(data_get($setUp, 'detail.viewIcon'))
            @unless (data_get($setUp, 'detail.viewIcon'))
                <div
                    x-bind:class='{
                        "rotate-90": collapsed,
                        "-rotate-0": !collapsed
                    }'>
                    <x-livewire-powergrid::icons.arrow
                        class="text-pg-primary-600 w-5 h-5 transition-all duration-300 dark:text-pg-primary-200"
                    />
                </div>
                @endif
            </div>
        </div>
    </td>
