@props([
    'theme' => '',
    'class' => '',
    'column' => null,
    'inline' => null,
    'select' => null
])
<div>
    @if(filled($select))
        <div @class([
            'pt-2 p-2' => !$inline,
            $theme->baseClass,
        ]) style="{{ $theme->baseStyle }}">
            @if(!$inline)
                <label for="input_{{ data_get($select, 'dataField') }}" class="text-slate-700 dark:text-slate-300">{{ data_get($select, 'label')  }}</label>
            @endif
            <div class="relative">
                <select id="input_{!! data_get($select, 'displayField') !!}"
                        class="power_grid {{ $theme->inputClass }} {{ $class }} {{ data_get($column, 'headerClass') }}"
                        style="{{ data_get($column, 'headerStyle') }}"
                        wire:input.debounce.500ms="filterSelect('{{ data_get($select, 'dataField') }}','{{ data_get($select, 'label')  }}')"
                        wire:model.debounce.500ms="filters.select.{{ data_get($select, 'dataField')  }}">
                    <option>{{ trans('livewire-powergrid::datatable.select.all') }}</option>
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
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700 dark:bg-slate-500 dark:text-slate-200 dark:placeholder-slate-200 dark:border-slate-500">
                    <x-livewire-powergrid::icons.down class="w-4 h-4"/>
                </div>
            </div>
        </div>
    @endif
</div>
