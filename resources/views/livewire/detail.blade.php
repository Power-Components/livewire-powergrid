<tr
    x-data="{ collapsed: @entangle('show'), singleExpand: @entangle('singleExpand') }"
    class="{{ $trClass }}"
>
    <td
        x-show="collapsed || (collapsed && singleExpand && expandedId == '{{ $rowId }}')"
        colspan="999"
    >
        @includeWhen($show, $view, [
            'id' => $rowId,
            'options' => $options,
        ])
    </td>
</tr>
