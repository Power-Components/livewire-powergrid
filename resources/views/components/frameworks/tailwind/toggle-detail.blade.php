<td
    class="{{ theme_style($theme, 'table.body.td') }}"
>
    @php
        $realPrimaryKey = $row->{$this->realPrimaryKey};
    @endphp
    <div
        class="cursor-pointer flex items-center"
        x-on:click.prevent="$wire.toggleDetail('{{ $realPrimaryKey }}')"
    >
        <div wire:loading wire:target="toggleDetail('{{ $realPrimaryKey }}')">
            <x-livewire-powergrid::icons.loading
                class="text-pg-primary-300 dark:text-pg-primary-400 h-5 w-5 animate-spin"
            />
        </div>

        <div wire:loading.remove wire:target="toggleDetail('{{ $realPrimaryKey }}')">
            @includeIf(data_get($setUp, 'detail.viewIcon'))
            @unless(data_get($setUp, 'detail.viewIcon'))
                <x-livewire-powergrid::icons.arrow
                    @class([
                        'rotate-90' => data_get($setUp, 'detail.state.' . $rowId),
                        '-rotate-0' => !data_get($setUp, 'detail.state.' . $rowId),
                        'text-pg-primary-600 w-5 h-5 transition-all duration-300 dark:text-pg-primary-200',
                    ])
                />
            @endif
        </div>
    </div>
</td>
