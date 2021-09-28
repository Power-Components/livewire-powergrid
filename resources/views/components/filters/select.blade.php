@props([
    'theme' => '',
    'inline' => null,
    'select' => null
])
<div>

    @if(filled($select))
        <div class="@if($inline) {{ $theme->divClassInline }} @endif {{ $theme->divClassNotInline }}{!! (data_get($select, 'label') != '') ?? '' !!}">
            @if(!$inline)
                <label for="input_{{ data_get($select, 'relation_id') }}" class="text-gray-700 dark:text-gray-300">{{ data_get($select, 'label')  }}</label>
            @endif
            <div class="relative">
                <select id="input_{!! data_get($select, 'relation_id') !!}"
                        class="power_grid {{ $theme->inputClass }} {{ (isset($class)) ? $class : 'w-9/12' }}"
                        wire:input.debounce.500ms="filterSelect('{{ data_get($select, 'relation_id') }}','{{ data_get($select, 'label')  }}')"
                        wire:model.debounce.500ms="filters.select.{{ data_get($select, 'relation_id')  }}"
                        data-live-search="{{ data_get($select, 'live-search') }}">
                    <option value="">{{ trans('livewire-powergrid::datatable.select.all') }}</option>
                    @foreach(data_get($select, 'data_source') as $relation)
                        <option value="{{ data_get($select, 'id')  }}">{{ $relation[data_get($select, 'display_field') ] }}</option>
                    @endforeach
                </select>
                <div class="{{ $theme->relativeDivClass }}">
                    <x-livewire-powergrid::icons.down/>
                </div>
            </div>
        </div>
    @endif

</div>
