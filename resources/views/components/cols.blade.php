@props([
    'column' => null,
    'theme' => null,
    'multiSort' => false,
    'sortArray' => [],
    'sortField' => null,
    'sortDirection' => null,
    'enabledFilters' => null,
    'actions' => null,
    'dataField' => null,
])
@php
    $field = filled($column->dataField) ? $column->dataField : $column->field;
@endphp
<th class="{{ $theme->table->thClass .' '. $column->headerClass }}"
    wire:key="{{ md5($column->field) }}"
    @if($column->sortable)
    x-data x-multisort-shift-click="{{ $this->id }}"
    @endif
    style="{{ $column->hidden === true ? 'display:none': '' }}; width: max-content; @if($column->sortable) cursor:pointer; @endif {{ $theme->table->thStyle.' '. $column->headerStyle }}">
    <div class="{{ $theme->cols->divClass }}"
        @if($column->sortable) wire:click="sortBy('{{ $field }}')" @endif>
        @if($column->sortable)
            <span>
                @if($multiSort && array_key_exists($field,$sortArray))
                    @if ($sortArray[$field] == 'desc')
                        &#8595;
                    @else
                        &#8593;
                    @endif
                @elseif($multiSort)
                    &#8597;
                @else
                    @if ($sortField !== $field)
                        &#8597;
                    @elseif ($sortDirection == 'desc')
                        &#8593;
                    @else
                        &#8595;
                    @endif
                @endif
			</span>
        @else
            <span style="width: 6px"></span>
        @endif
        <span>{{ $column->title }}</span>
    </div>
</th>
