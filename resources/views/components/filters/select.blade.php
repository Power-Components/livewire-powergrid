@props([
    'theme' => '',
    'inline' => null,
    'select' => null
])
<div>
    @if(filled($select))
        <div wire:ignore class="@if($inline) {{ $theme->divClassInline }} @endif {{ $theme->divClassNotInline }}{!! ($select['class'] != '') ?? '' !!}">
            @if(!$inline)
                <label for="input_{!! $select['relation_id'] !!}">{{$select['label']}}</label>
            @endif
            <div class="relative">
                <select id="input_{!! $select['relation_id'] !!}"
                        class="{{ $theme->inputClass }}{{ (isset($class)) ? $class : 'w-9/12' }}"
                        wire:model.lazy="filters.select.{!! $select['relation_id'] !!}"
                        wire:ignore
                        data-live-search="{{ $select['live-search'] }}">
                    <option value="">{{ trans('livewire-powergrid::datatable.select.all') }}</option>
                    @foreach($select['data_source'] as $relation)
                        <option value="{{ $relation['id'] }}">{{ $relation[$select['display_field']] }}</option>
                    @endforeach
                </select>
                <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <x-livewire-powergrid::icons.down/>
                </div>
            </div>
        </div>

    @endif

</div>
