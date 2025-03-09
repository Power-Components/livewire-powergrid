{{-- blade-formatter-enable --}}
<tr
    class="{{ theme_style($theme, 'table.footer.tr') }}"
>
    @if (data_get($setUp, 'detail.showCollapseIcon'))
        <td
            class="{{ theme_style($theme, 'table.body.td') }}"
        ></td>
    @endif
    @if ($checkbox)
        <td></td>
    @endif
    @foreach ($this->visibleColumns as $column)
        <td
            class="{{ theme_style($theme, 'table.body.tdSummarize') . ' ' . data_get($column, 'bodyClass') ?? '' }}"
            style="{{ data_get($column, 'hidden') === true ? 'display:none;' : '' }} . {{ data_get($column, 'bodyStyle') ?? '' }}"
        >
            @include('livewire-powergrid::components.summarize', [
                'sum' => data_get($column, 'properties.summarize.sum.footer') ? data_get($column, 'properties.summarize_values.sum') : null,
                'labelSum' =>  data_get($column, 'properties.summarize.sum.label'),

                'count' =>  data_get($column, 'properties.summarize.count.footer') ? data_get($column, 'properties.summarize_values.count') : null,
                'labelCount' =>  data_get($column, 'properties.summarize.count.label'),

                'min' =>  data_get($column, 'properties.summarize.min.footer') ? data_get($column, 'properties.summarize_values.min') : null,
                'labelMin' =>  data_get($column, 'properties.summarize.min.label'),

                'max' =>  data_get($column, 'properties.summarize.max.footer') ? data_get($column, 'properties.summarize_values.max') : null,
                'labelMax' =>  data_get($column, 'properties.summarize.max.label'),

                'avg' =>  data_get($column, 'properties.summarize.avg.footer') ? data_get($column, 'properties.summarize_values.avg') : null,
                'labelAvg' =>  data_get($column, 'properties.summarize.avg.label'),
            ])
        </td>
    @endforeach
    @if (isset($actions) && count($actions))
        <th
            class="{{ theme_style($theme, 'table.header.th') . ' ' .  data_get($column, 'headerClass') }}"
            scope="col"
            colspan="{{ count($actions) }}"
        >
        </th>
    @endif
</tr>
{{-- blade-formatter-enable --}}
