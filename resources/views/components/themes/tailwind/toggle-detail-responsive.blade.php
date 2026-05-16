<td
    x-cloak
    x-show="hasHiddenElements"
    class="w-0 {{ theme('table.layout.td') }}"
>
    <button
        class="flex items-center"
        x-on:click="toggleExpanded('{{ $rowId }}')"
    >
        <x-livewire-powergrid::icons.arrow
            class="{{ theme('table.body.tr.responsive_toggle_icon') }} w-5 h-5 transition-all duration-300"
            x-bind:class="expanded == '{{ $rowId }}' ? 'rotate-90' : '-rotate-0'"
        />
    </button>
</td>
