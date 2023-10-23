{{-- blade-formatter-enable --}}
<tr
    class="{{ $theme->table->trBodyClass }}"
    style="{{ $theme->table->trBodyStyle }}"
>
    @if (data_get($setUp, 'detail.showCollapseIcon'))
        <td
            class="{{ $theme->table->tdBodyClass }}"
            style="{{ $theme->table->tdBodyStyle }}"
        ></td>
    @endif
    @if ($checkbox)
        <td></td>
    @endif
    @foreach ($this->visibleColumns as $column)
        <td
            class="{{ $theme->table->tdBodyClassTotalColumns . ' ' . $column->bodyClass ?? '' }}"
            style="{{ $column->hidden === true ? 'display:none' : '' }}; {{ $theme->table->tdBodyStyleTotalColumns . ' ' . $column->bodyStyle ?? '' }}"
        >
            @include('livewire-powergrid::components.summarize', [
                'sum' => $column->sum['footer'] ? data_get($column, 'summarize.sum') : null,
                'labelSum' => $column->sum['label'],

                'count' => $column->count['footer'] ? data_get($column, 'summarize.count') : null,
                'labelCount' => $column->count['label'],

                'min' => $column->min['footer'] ? data_get($column, 'summarize.min') : null,
                'labelMin' => $column->min['label'],

                'max' => $column->max['footer'] ? data_get($column, 'summarize.max') : null,
                'labelMax' => $column->max['label'],

                'avg' => $column->avg['footer'] ? data_get($column, 'summarize.avg') : null,
                'labelAvg' => $column->avg['label'],
            ])
        </td>
    @endforeach
    @if (isset($actions) && count($actions))
        <th
            class="{{ $theme->table->thClass . ' ' . $column->headerClass }}"
            scope="col"
            style="{{ $theme->table->thStyle }}"
            colspan="{{ count($actions) }}"
        >
        </th>
    @endif
</tr>
{{-- blade-formatter-enable --}}
