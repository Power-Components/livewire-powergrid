@props([
    'column' => null,
    'theme' => null,
    'sortField' => null,
    'sortDirection' => null,
    'enabledFilters' => null,
    'actions' => null,
    'dataField' => null,
])
@php
    if (filled($column->dataField)) {
        $field = $column->dataField;
    } else {
        $field = $column->field;
    }
@endphp
<th class="{{ $theme->table->thClass .' '. $column->headerClass }}"
    wire:key="{{ md5($column->field) }}"
    style="{{ $column->hidden === true ? 'display:none': '' }}; width: max-content; @if($column->sortable) cursor:pointer; @endif {{ $theme->table->thStyle.' '. $column->headerStyle }}">
    <div class="{{ $theme->cols->divClass }}"
        @if($column->sortable === true) wire:click="sortBy('{{ $field }}')" @endif>
        @if($column->sortable === true)
            <span class="text-md pr-2">
				@if ($sortField !== $field)
                    &#8597;
                @elseif ($sortDirection == 'desc')
                    &#8593;
                @else
                    &#8595;
                @endif
					</span>
        @endif
        <span>{!! $column->title !!}</span>
    </div>
</th>
