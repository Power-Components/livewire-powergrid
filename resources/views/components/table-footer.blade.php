{{-- blade-formatter-enable --}}
<tr
    class="{{ data_get($theme, 'table.trBodyClass') }}"
    style="{{ data_get($theme, 'table.trBodyStyle') }}"
>
    @if (data_get($setUp, 'detail.showCollapseIcon'))
        <td
            class="{{ data_get($theme, 'table.tdBodyClass') }}"
            style="{{ data_get($theme, 'table.tdBodyStyle') }}"
        ></td>
    @endif
    @if ($checkbox)
        <td></td>
    @endif
    @foreach ($this->visibleColumns as $column)
        <td
            class="{{ data_get($theme, 'table.tdBodyClassTotalColumns') . ' ' . data_get($column, 'bodyClass') ?? '' }}"
            style="{{ data_get($column, 'hidden') === true ? 'display:none' : '' }}; {{ data_get($theme, 'table.tdBodyStyleTotalColumns') . ' ' . data_get($column, 'bodyStyle') ?? '' }}"
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
            class="{{ data_get($theme, 'table.thClass') . ' ' .  data_get($column, 'headerClass') }}"
            scope="col"
            style="{{ data_get($theme, 'table.thStyle') }}"
            colspan="{{ count($actions) }}"
        >
        </th>
    @endif
</tr>
{{-- blade-formatter-enable --}}
