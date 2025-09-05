<tbody>
    @foreach ($data as $row)
        @php
            $rowId = data_get($row, $this->realPrimaryKey);
            $class = theme_style($theme, 'table.body.tr');
        @endphp

        @if (isset($setUp['detail']))
            <tr class="{{ $class }}">
                @include('livewire-powergrid::components.row', [
                    'rowIndex' => $loop->index + 1,
                    'childIndex' => $childIndex,
                    'parentId' => $parentId
                ])
            </tr>

            @php
                $hasDetailView = (bool) data_get(
                    collect($row->__powergrid_rules)->where('apply', true)->last(),
                    'detailView',
                );

                if ($hasDetailView) {
                    $detailView = data_get($row->__powergrid_rules, '0.detailView');
                    $rulesValues = data_get($row->__powergrid_rules, '0.options', []);
                } else {
                    $detailView = data_get($setUp, 'detail.view');
                    $rulesValues = data_get($setUp, 'detail.options', []);
                }
            @endphp
            <livewire:powergrid-detail
                key="powergrid-lazy-child-detail-{{ $rowId }}"
                :view="$detailView"
                :options="$rulesValues"
                :row-id="$rowId"
                tr-class="{{ $class }}"
                :row="$row->toArray()"
                :collapse-others="data_get($setUp, 'detail.collapseOthers', false)"
             />
        @else
            <tr
                class="{{ $class }}"
            >
                @include('livewire-powergrid::components.row', [
                    'rowIndex' => $loop->index + 1,
                ])
            </tr>
        @endif

        @includeWhen(isset($setUp['responsive']), 'livewire-powergrid::components.expand-container')
    @endforeach
</tbody>
