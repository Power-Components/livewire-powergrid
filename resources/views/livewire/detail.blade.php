<tr
    x-data="{ collapsed: @entangle('show') }"
    class="{{ $trClass }}"
>
    <td
        x-show="collapsed"
        colspan="999"
    >
        @includeWhen($show, $view, [
            'id' => $rowId,
            'options' => [],
        ])
    </td>
</tr>
