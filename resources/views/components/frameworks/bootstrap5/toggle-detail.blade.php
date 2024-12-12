<td class="{{ theme_style($theme, 'table.body.td') }}">
    <div x-on:click.prevent="$wire.toggleDetail('{{ $row->{$this->realPrimaryKey} }}')">
        @includeIf(data_get($setUp, 'detail.viewIcon'))

        @if (!data_get($setUp, 'detail.viewIcon'))
            <x-livewire-powergrid::icons.arrow
                @class([
                    'bs5-rotate-90' => data_get($setUp, 'detail.state.' . $rowId),
                    'bs5-rotate-0' => !data_get($setUp, 'detail.state.' . $rowId),
                    'bs5-w-h-1_5em',
                ])
                style="cursor:pointer"
            />
        @endif
    </div>
</td>
