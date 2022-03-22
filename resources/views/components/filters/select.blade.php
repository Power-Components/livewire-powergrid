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
                <label for="input_{{ data_get($select, 'data_field') }}" class="text-gray-700 dark:text-gray-300">{{ data_get($select, 'label')  }}</label>
            @endif
            <div class="relative">
                <select id="input_{!! data_get($select, 'data_field') !!}"
                        class="power_grid {{ $theme->inputClass }} {{ $class }} {{ data_get($column, 'headerClass') }}"
                        style="{{ data_get($column, 'headerStyle') }}"
                        wire:input.debounce.500ms="filterSelect('{{ data_get($select, 'data_field') }}','{{ data_get($select, 'label')  }}')"
                        wire:model.debounce.500ms="filters.select.{{ data_get($select, 'data_field')  }}"
                        data-live-search="{{ data_get($select, 'live-search') }}">
                    <option value="">{{ trans('livewire-powergrid::datatable.select.all') }}</option>
                    @foreach(data_get($select, 'data_source') as $relation)
                        @php 
                            $key = isset($relation['id']) ? 'id' : 'value';
                            if (isset($relation[$select['data_field']])) $key = $select['data_field']; 
                        @endphp
                        <option value="{{ data_get($relation, $key) }}">
                            {{ $relation[data_get($select, 'display_field') ] }}
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
