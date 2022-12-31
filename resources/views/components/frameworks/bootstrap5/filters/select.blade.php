@props([
    'theme' => '',
    'class' => '',
    'column' => null,
    'inline' => null,
    'filter' => null
])
<div>
    @php
        $field = strval(data_get($filter, 'field'));
        $title = strval(data_get($filter, 'title'));

        $filterClasses = Arr::toCssClasses([
            $theme->selectClass, $class, data_get($column, 'headerClass'), 'power_grid'
        ])
    @endphp
    @if(filled($filter))
        <div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
            <select id="{{ $field }}"
                    class="{{ $filterClasses }}"
                    style="{{ data_get($column, 'headerStyle') }}"
                    wire:input.debounce.500ms="filterSelect('{{ $field }}','{{ $title  }}')"
                    wire:model.debounce.500ms="filters.select.{{ $field  }}">
                <option value="">{{ trans('livewire-powergrid::datatable.select.all') }}</option>
                @foreach(data_get($filter, 'dataSource') as $key => $item)
                    <option wire:key="select-{{ $tableName }}-{{ $key }}"
                            value="{{ $item[data_get($filter, 'optionValue')] }}">
                        {{ $item[data_get($filter, 'optionLabel')] }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</div>
