<tbody>
    @foreach ($data as $row)
        @php
            $rowId = data_get($row, $this->realPrimaryKey);

            $class = data_get($theme, 'table.trBodyClass');

            $this->actionRulesForRows[$rowId] = $this->prepareActionRulesForRows($row, $loop);
        @endphp

        @if (isset($setUp['detail']))
            <tr class="{{ $class }}">
                @include('livewire-powergrid::components.row', [
                    'rowIndex' => $loop->index + 1,
                    'childIndex' => $childIndex,
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
