<th>
    <tr
        class="{{ $theme->table->trBodyClass }}"
        style="{{ $theme->table->trBodyStyle }}"
    >
        <td
            class="{{ $theme->table->tdBodyEmptyClass }}"
            style="{{ $theme->table->tdBodyEmptyStyle }}"
            colspan="{{ ($checkbox ? 1 : 0) + count($columns) + (data_get($setUp, 'detail.showCollapseIcon') ? 1 : 0) }}"
        >
            <span>{{ trans('livewire-powergrid::datatable.labels.no_data') }}</span>
        </td>
    </tr>
</th>
