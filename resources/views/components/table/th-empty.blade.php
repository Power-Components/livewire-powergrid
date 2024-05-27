<tr
    class="{{ data_get($theme, 'table.trBodyClass') }}"
    style="{{ data_get($theme, 'table.trBodyStyle') }}"
>
    <th
        class="{{ data_get($theme, 'table.tdBodyEmptyClass') }}"
        style="{{ data_get($theme, 'table.tdBodyEmptyStyle') }}"
        colspan="999"
    >
            {!! $this->processNoDataLabel() !!}
    </th>
</tr>
