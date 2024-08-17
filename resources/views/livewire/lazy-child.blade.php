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
            @if (data_get($setUp, 'detail.state.' . $rowId))
                <tr class="{{ $class }}">
                    @include('livewire-powergrid::components.table.detail')
                </tr>
            @endif
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
