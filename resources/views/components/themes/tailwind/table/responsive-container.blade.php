<tr
    x-cloak
    expand
    data-expand-for="{{ $rowId }}"
    wire:key="expand-{{ $rowId }}"
    x-show="isExpanded('{{ $rowId }}')"
    x-transition
    class="{{ theme('table.body.tr.responsive') }}"
>
    <td colspan="999">
        <div class="flex gap-x-6 gap-y-2 flex-wrap p-2 responsive-row-expand-container"></div>
    </td>
</tr>
