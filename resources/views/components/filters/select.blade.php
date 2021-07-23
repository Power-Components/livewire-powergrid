@props([
    'theme' => '',
    'inline' => null,
    'select' => null
])
<div>

    @if(filled($select))
        <div class="@if($inline) {{ $theme->divClassInline }} @endif {{ $theme->divClassNotInline }}{!! ($select['class'] != '') ?? '' !!}">
            @if(!$inline)
                <label for="input_{!! $select['relation_id'] !!}" class="text-gray-700 dark:text-gray-300">{{ $select['label'] }}</label>
            @endif
            <div class="relative">
                <select id="input_{!! $select['relation_id'] !!}"
                        class="power_grid {{ $theme->inputClass }} {{ (isset($class)) ? $class : 'w-9/12' }}"
                        wire:input.lazy="filterSelect('{{ $select['relation_id'] }}','{{ $select['label'] }}')"
                        wire:model.lazy="filters.select.{{ $select['relation_id'] }}"
                        data-live-search="{{ $select['live-search'] }}">
                    <option value="">{{ trans('livewire-powergrid::datatable.select.all') }}</option>
                    @foreach($select['data_source'] as $relation)
                        <option value="{{ $relation['id'] }}">{{ $relation[$select['display_field']] }}</option>
                    @endforeach
                </select>
                <div class="{{ $theme->relativeDivClass }}">
                    <x-livewire-powergrid::icons.down/>
                </div>
            </div>
        </div>
    @endif

</div>
