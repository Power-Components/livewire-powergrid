@if(data_get($setUp, 'detail.state.' . $rowId))
    @php
        $rulesValues = $actionRulesClass->recoverFromAction($row, 'pg:rows');
    @endphp

    <td colspan="999">
        @if (filled($rulesValues['detailView']))
            @includeWhen(data_get($setUp, 'detail.state.' . $row->{$primaryKey}),
                $rulesValues['detailView'][0]['detailView'],
                [
                    'id' => data_get($row, $primaryKey),
                    'options' => array_merge(
                        data_get($setUp, 'detail.options'),
                        $rulesValues['detailView']['0']['options']),
                ]
            )
        @else
            @includeWhen(data_get($setUp, 'detail.state.' . $row->{$primaryKey}),
                data_get($setUp, 'detail.view'),
                [
                    'id' => data_get($row, $primaryKey),
                    'options' => data_get($setUp, 'detail.options'),
                ]
            )
        @endif
    </td>
@endif
