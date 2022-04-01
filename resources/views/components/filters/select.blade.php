@props([
    'theme' => '',
    'class' => '',
    'column' => null,
    'inline' => null,
    'select' => null
])
<div>

    @if(filled($select))
        <div class="@if(!$inline) pt-2 p-2 @endif">
            @if(!$inline)
                <label for="input_{{ data_get($select, 'dataField') }}" class="text-gray-700 dark:text-gray-300">{{ data_get($select, 'label')  }}</label>
            @endif
            <div class="relative">
                <select id="input_{!! data_get($select, 'displayField') !!}"
                        class="power_grid {{ $theme->inputClass }} {{ $class }} {{ data_get($column, 'headerClass') }}"
                        style="{{ data_get($column, 'headerStyle') }}"
                        wire:input.debounce.500ms="filterSelect('{{ data_get($select, 'dataField') }}','{{ data_get($select, 'label')  }}')"
                        wire:model.debounce.500ms="filters.select.{{ data_get($select, 'dataField')  }}"
                        data-live-search="true">
                    <option value="">{{ trans('livewire-powergrid::datatable.select.all') }}</option>
                    @foreach(data_get($select, 'data_source') as $relation)
                        @php
                            $key = isset($relation['id']) ? 'id' : 'value';
                            if (isset($relation[$select['dataField']])) $key = $select['dataField'];
                        @endphp
                        <option value="{{ data_get($relation, $key) }}">
                            {{ $relation[data_get($select, 'displayField') ] }}
                        </option>
                    @endforeach
                </select>
                <div class="{{ $theme->relativeDivClass }}">
                    <x-livewire-powergrid::icons.down class="w-4 h-4"/>
                </div>
            </div>
        </div>
    @endif

</div>
