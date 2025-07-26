<td
    x-data="() => {
        return {
            collapsed: false,
            loading: false,
            collapseOthers: @js(data_get($setUp, 'detail.collapseOthers', false)),
            toggleDetail() {
                const isOpen = this.collapsed;
    
                if (this.collapseOthers) {
                    this.$dispatch('toggle-detail-hidden-all-{{ $tableName }}');
                    expandedId = '{{ $rowId }}';
                } else {
                    this.loading = true;
                }
    
                this.collapsed = !isOpen;
    
                this.$dispatch('toggle-detail-{{ $rowId }}', {
                    collapsed: this.collapsed
                });
            }
        }
    }"
    x-on:toggle-detail-hidden-all-{{ $tableName }}.window="collapsed = false"
    x-on:powergrid-detail-loaded.window="loading = false"
    class="{{ theme_style($theme, 'table.body.td') }}"
>
    <div
        class="cursor-pointer flex items-center"
        x-on:click="toggleDetail"
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
                    x-bind:class="{
                        'rotate-90': collapsed && expandedId == '{{ $rowId }}',
                        '-rotate-0': !collapsed
                    }">
                    <x-livewire-powergrid::icons.arrow
                        class="text-pg-primary-600 w-5 h-5 transition-all duration-300 dark:text-pg-primary-200"
                    />
                </div>
            @endunless
        </div>
    </div>
</td>
