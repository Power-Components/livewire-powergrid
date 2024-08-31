<td
    class="{{ theme_style($theme, 'table.body.td') }}"
>
    <div
        class="cursor-pointer"
        x-on:click.prevent="$wire.toggleDetail('{{ $row->{$this->realPrimaryKey} }}')"
    >
        @includeIf(data_get($setUp, 'detail.viewIcon'))
        @if (!data_get($setUp, 'detail.viewIcon'))
            <x-livewire-powergrid::icons.arrow
                @class([
                    'rotate-90' => data_get($setUp, 'detail.state.' . $rowId),
                    '-rotate-0' => !data_get($setUp, 'detail.state.' . $rowId),
                    'text-pg-primary-600 w-5 h-5 transition-all duration-300 dark:text-pg-primary-200',
                ])
            />
        @endif
    </div>
</td>
