<tr
    x-cloak
    expand
    wire:key="{{ 'expand-' . substr($rowId, 0, 6) }}"
    x-show="hasHiddenElements && expanded == '{{ $rowId }}'"
    x-transition
    class="{{ theme('table.body.tr.responsive') }}"
>
    <td colspan="999">
        <div class="flex gap-x-6 gap-y-2 flex-wrap p-2 responsive-row-expand-container"></div>
    </td>
</tr>
