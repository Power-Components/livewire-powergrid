<td
    class="{{ $theme->tdBodyClass }}"
    style="{{ $theme->tdBodyStyle }}"
>
    <div
        class="cursor-pointer"
        x-on:click.prevent="$wire.toggleDetail('{{ $row->{$primaryKey} }}')"
    >
        @includeIf(data_get($setUp, 'detail.viewIcon'))

        @if (!data_get($setUp, 'detail.viewIcon'))
            <x-livewire-powergrid::icons.arrow x-show="!detailState" />
            <x-livewire-powergrid::icons.chevron-down x-show="detailState" />
        @endif
    </div>
</td>
