<tr
    data-table-name="{{ $tableName }}"
    data-row-id="{{ $rowId }}"
    class="{{ $trClass }}"
    @unless ($show) hidden @endunless
>
    <td colspan="999">
        @includeWhen($show, $view, [
            'id' => $rowId,
            'options' => $options,
        ])
    </td>
</tr>
