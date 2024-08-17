{{-- blade-formatter-enable --}}
<tr
    class="{{ theme_style($theme, 'table.header.tr') }}"
    style="{{theme_style($theme, 'table.header.tr.1') }}"
>
    @if (data_get($setUp, 'detail.showCollapseIcon'))
        <td
            class="{{ theme_style($theme, 'table.body.td') }}"
            style="{{ theme_style($theme, 'table.body.td.1')  }}"
        ></td>
    @endif
    @if ($checkbox)
        <td></td>
    @endif
    @foreach ($this->visibleColumns as $column)
        <td
            class="{{ theme_style($theme, 'table.body.tdSummarize') . ' ' . data_get($column, 'bodyClass') ?? '' }}"
            style="{{ data_get($column, 'hidden') === true ? 'display:none' : '' }}; {{ theme_style($theme, 'table.body.tdSummarize.1') . ' ' . data_get($column, 'bodyStyle') ?? '' }}"
        >
            @include('livewire-powergrid::components.summarize', [
                'sum' => data_get($column, 'sum.footer') ? data_get($column, 'summarize.sum') : null,
                'labelSum' =>  data_get($column, 'sum.label'),

                'count' =>  data_get($column, 'count.footer') ? data_get($column, 'summarize.count') : null,
                'labelCount' =>  data_get($column, 'count.footer'),

                'min' =>  data_get($column, 'min.footer') ? data_get($column, 'summarize.min') : null,
                'labelMin' =>  data_get($column, 'min.footer'),

                'max' =>  data_get($column, 'max.footer') ? data_get($column, 'summarize.max') : null,
                'labelMax' =>  data_get($column, 'max.label'),

                'avg' =>  data_get($column, 'avg.footer') ? data_get($column, 'summarize.avg') : null,
                'labelAvg' =>  data_get($column, 'avg.label'),
            ])
        </td>
    @endforeach
    @if (isset($actions) && count($actions))
        <th
            class="{{ theme_style($theme, 'table.header.th') . ' ' .  data_get($column, 'headerClass') }}"
            scope="col"
            style="{{ theme_style($theme, 'table.header.th.1') }}"
            colspan="{{ count($actions) }}"
        >
        </th>
    @endif
</tr>
{{-- blade-formatter-enable --}}
