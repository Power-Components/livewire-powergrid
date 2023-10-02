<template
    x-cloak
    x-if="detailState"
>
    <td colspan="999">
        @if (data_get($setUp, 'detail.state.' . $row->{$primaryKey}))
            @php
                $rulesValues = $actionRulesClass->recoverFromAction($row, 'pg:rows');
            @endphp

            @if (filled($rulesValues['detailView']))
                @include($rulesValues['detailView'][0]['detailView'], [
                    'id' => data_get($row, $primaryKey),
                    'options' => array_merge(
                        data_get($setUp, 'detail.options'),
                        $rulesValues['detailView']['0']['options']),
                ])
            @else
                @include(data_get($setUp, 'detail.view'), [
                    'id' => data_get($row, $primaryKey),
                    'options' => data_get($setUp, 'detail.options'),
                ])
            @endif
        @endif
    </td>
</template>
