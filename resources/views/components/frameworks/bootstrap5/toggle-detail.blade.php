<td
    x-data="() => {
        return {
            collapsed: false,
            loading: false,
            collapseOthers: @js(data_get($setUp, 'detail.collapseOthers', false)),
            toggleDetail() {
                const isOpen = this.collapsed;
    
                if (this.collapseOthers) {
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
    x-on:pg-toggle-detail-{{ $tableName }}-loaded.window="loading = false;"
    class="{{ theme_style($theme, 'table.body.td') }}"
>
    <div x-on:click="toggleDetail()">
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
