<tbody x-cloak expand wire:key="{{ md5('expand-' . $rowId) }}" x-show="hasHiddenElements">
    <tr x-show="expanded == {{ $rowId }}" x-transition
        class="text-slate-400 border-slate-100 break-words w-full text-sm">
        <td colspan="999">
            <div class="flex gap-x-6 gap-y-2 flex-wrap p-2 responsive-row-expand-container"></div>
        </td>
    </tr>
</tbody>
