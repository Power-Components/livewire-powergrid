<tr
    class="{{ theme_style($theme, 'table.header.tr') }}"
    style="{{ theme_style($theme, 'table.header.tr.1') }}"
>
    <th
        class="{{ theme_style($theme, 'table.body.tdEmpty') }}"
        style="{{ theme_style($theme, 'table.body.tdEmpty.1') }}"
        colspan="999"
    >
        {!! $this->processNoDataLabel() !!}
    </th>
</tr>
