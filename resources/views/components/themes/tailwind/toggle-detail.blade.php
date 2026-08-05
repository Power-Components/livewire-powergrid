<td
    x-data="pgToggleDetail"
    data-single-expand="{{ data_get($setUp, 'detail.singleExpand', false) ? 'true' : 'false' }}"
    data-table-name="{{ $tableName }}"
    data-row-id="{{ $rowId }}"
    x-on:pg-toggle-detail-{{ $tableName }}-hidden-all.window="resetCollapsed()"
    x-on:pg-toggle-detail-{{ strtolower($tableName) }}-loaded.window="stopLoading()"
    class="{{ theme('table.layout.td') }}"
>
    <div
        class="cursor-pointer flex items-center"
        x-on:click="toggleDetail()"
    >
        <div x-show="loading" x-cloak>
            <x-livewire-powergrid::icons.loading
                class="text-zinc-300 dark:text-zinc-400 size-4 animate-spin"
            />
        </div>

        <div x-show="notLoading()">
            @includeIf(data_get($setUp, 'detail.viewIcon'))

            @unless (data_get($setUp, 'detail.viewIcon'))
                <div x-bind:class="iconClass()">
                    <x-livewire-powergrid::icons.arrow
                        class="text-zinc-600 w-5 h-5 transition-all duration-300 dark:text-zinc-200"
                    />
                </div>
            @endunless
        </div>
    </div>
</td>
