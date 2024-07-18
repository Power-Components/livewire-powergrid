<td
    class="{{ data_get($theme, 'tdBodyClass') }}"
    style="{{  data_get($theme, 'tdBodyStyle') }}"
>
    <div
        class="cursor-pointer"
        x-on:click.prevent="$wire.toggleDetail('{{ $row->{$primaryKey} }}')"
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
