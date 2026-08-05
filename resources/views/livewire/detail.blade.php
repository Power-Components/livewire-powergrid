<tr
    x-data="pgDetailRow"
    data-table-name="{{ $tableName }}"
    data-row-id="{{ $rowId }}"
    class="{{ $trClass }}"
>
    <td
        x-show="visible()"
        colspan="999"
    >
        @includeWhen($show, $view, [
            'id' => $rowId,
            'options' => $options,
        ])
    </td>
</tr>
