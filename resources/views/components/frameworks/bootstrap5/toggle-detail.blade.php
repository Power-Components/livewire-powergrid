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
    x-on:powergrid-detail-loaded.window="loading = false;"
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
