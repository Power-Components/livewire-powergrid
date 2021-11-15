@props([
    'column' => null,
    'theme' => null,
    'sortField' => null,
    'sortDirection' => null,
    'enabledFilters' => null,
    'actions' => null,
    'dataField' => null,
    'currentTable' => null,
])
<div>

    @if($column->hidden === false)
        @php
            $tableWithColumn = $column->tableWithColumn;

            if (\Illuminate\Support\Str::of($column->tableWithColumn)->contains($currentTable)) {
                $tableWithColumn = \Illuminate\Support\Str::replace($currentTable.".", $column->tableWithColumn, '');
            }
        @endphp
        <th class="{{ $theme->table->thClass .' '. $column->headerClass }}"
            style="width: max-content;@if($column->sortable)cursor:pointer; @endif{{ $theme->table->thStyle.' '. $column->headerStyle }}">
            <div class="{{ $theme->cols->divClass }}"
                 @if($column->sortable === true)
                 wire:click="sortBy('{{ $tableWithColumn != '' ? $tableWithColumn : ($column->dataField !='' ? $column->dataField : $column->field) }}')"
                @endif
            >
                @if($column->sortable === true)
                    <span class="text-base pr-2" style="font-size: 1rem !important;">
						@if ($sortField !== $tableWithColumn)
                            &#8597;
                        @elseif ($sortDirection == 'desc')
                            &#8593;
                        @else
                            &#8595;
                        @endif
					</span>
                @endif
                {{ $column->title }}
            </div>
        </th>
    @endif
</div>
