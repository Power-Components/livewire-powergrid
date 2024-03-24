<tr
    class="{{ data_get($theme, 'table.trBodyClass') }}"
    style="{{ data_get($theme, 'table.trBodyStyle') }}"
>
    <th
        class="{{ data_get($theme, 'table.tdBodyEmptyClass') }}"
        style="{{ data_get($theme, 'table.tdBodyEmptyStyle') }}"
        colspan="{{ ($checkbox ? 1 : 0) + count($columns) + (data_get($setUp, 'detail.showCollapseIcon') ? 1 : 0) }}"
    >
            {!! $this->processNoDataLabel() !!}
    </th>
</tr>
