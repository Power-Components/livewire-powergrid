<td
    class="{{ theme_style($this->theme, 'table.body.td') }}"
    style="{{theme_style($this->theme, 'table.body.td.1') }}"
>
    <div
        class="cursor-pointer"
        x-on:click.prevent="$wire.toggleDetail('{{ $row->{$this->realPrimaryKey} }}')"
    >
        @includeIf(data_get($setUp, 'detail.viewIcon'))

        @if (!data_get($setUp, 'detail.viewIcon'))
            <x-livewire-powergrid::icons.arrow
                x-bind:class="detailState ? 'bs5-rotate-90' : 'bs5-rotate-0'"
                class="bs5-w-h-1_5em"
            />
        @endif
    </div>
</td>
