<tr
    x-data="{ collapsed: @entangle('show'), collapseOthers: @entangle('collapseOthers') }"
    class="{{ $trClass }}"
>
    <td
        x-show="collapsed || (collapsed && collapseOthers && expandedId == '{{ $rowId }}')"
        colspan="999"
    >
        @includeWhen($show, $view, [
            'id' => $rowId,
            'options' => $options,
        ])
    </td>
</tr>
