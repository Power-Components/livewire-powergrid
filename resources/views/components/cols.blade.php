@props([
    'column' => null,
    'theme' => null,
    'sortField' => null,
    'sortDirection' => null,
    'sortAscIcon' => null,
    'sortDescIcon' => null,
    'sortIcon' => null,
    'filtersEnabled' => null,
    'actions' => null
]
)
<div>
    @if($column->hidden === false)
        <th class="{{ $theme->thClass .' '. $column->header_class }}"
            style="width: max-content;@if($column->sortable)cursor:pointer; @endif{{ $theme->thStyle.' '. $column->header_style }}">
            <div class="align-content-between">
                @if($column->sortable === true)
                    <span class="text-base pr-2">
						@if ($sortField !== $column->field)
                            {!! $sortIcon !!}
                        @elseif ($sortDirection)
                            {!! $sortAscIcon !!}
                        @else
                            {!! $sortDescIcon !!}
                        @endif
					</span>
                @endif
                <span
                    @if($column->sortable === true)
                    wire:click="sortBy('{{$column->field}}')"
                    @endif
                >
                    {{$column->title}}
                </span>

                <x-livewire-powergrid::clear_filter
                    :filtersEnabled="$filtersEnabled"
                    :field="$column->field"
                />

            </div>
        </th>
    @endif
</div>
