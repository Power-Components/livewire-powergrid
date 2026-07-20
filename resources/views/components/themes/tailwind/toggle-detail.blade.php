<td
    x-data="() => {
        return {
            collapsed: false,
            loading: false,
            singleExpand: @js(data_get($setUp, 'detail.singleExpand', false)),
            toggleDetail() {
                const isOpen = this.collapsed;

                if (this.singleExpand) {
                    this.$dispatch('pg-toggle-detail-{{ $tableName }}-hidden-all');
                    expandedId = '{{ $rowId }}';
                }

                this.loading = true;
                this.collapsed = !isOpen;

                this.$dispatch('pg-toggle-detail-{{ $tableName }}-{{ $rowId }}', {
                    collapsed: this.collapsed
                });
            }
        }
    }"
    x-on:pg-toggle-detail-{{ $tableName }}-hidden-all.window="collapsed = false"
    x-on:pg-toggle-detail-{{ strtolower($tableName) }}-loaded.window="loading = false;"
    class="{{ theme('table.layout.td') }}"
>
    <div
        class="cursor-pointer flex items-center"
        x-on:click="toggleDetail"
    >
        <div x-show="loading">
            <x-livewire-powergrid::icons.loading
                class="text-zinc-300 dark:text-zinc-400 size-4 animate-spin"
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
                        class="text-zinc-600 w-5 h-5 transition-all duration-300 dark:text-zinc-200"
                    />
                </div>
            @endunless
        </div>
    </div>
</td>
