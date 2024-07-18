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
                class="text-pg-primary-600 w-5 h-5 transition-all duration-300 dark:text-pg-primary-200"
                x-bind:class="detailState ? 'rotate-90' : '-rotate-0'"
            />
        @endif
    </div>
</td>
