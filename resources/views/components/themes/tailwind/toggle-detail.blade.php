<td
    x-data="pgToggleDetail"
    data-table-name="{{ $tableName }}"
    data-row-id="{{ $rowId }}"
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
