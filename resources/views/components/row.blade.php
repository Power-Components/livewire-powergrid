@props([
    'theme' => null,
    'row' => null,
    'columns' => null
])
<div>
    @foreach($columns as $column)
        @php
            $field = $column->field;
        @endphp
        @if($column->hidden === false)
            <td class="{{ $theme->table->tdBodyClass . ' '.$column->bodyClass ?? '' }}"
                style=" {{ $theme->table->tdBodyStyle . ' '.$column->bodyStyle ?? '' }}"
            >
                @if($column->editable === true)
                    <span class="{{ $theme->editable->spanClass }}">
                        <x-livewire-powergrid::editable
                            :row="$row"
                            :theme="$theme->editable"
                            :field="$field"/>

                        <x-livewire-powergrid::click-to-copy
                            :row="$row"
                            :field="$row->{$field}"
                            :label="$column->click_to_copy['label'] ?? null"
                            :enabled="$column->click_to_copy['enabled'] ?? false"/>
                    </span>

                @elseif(count($column->toggleable) > 0)
                    @include($theme->toggleable->view)
                @else
                    <span class="flex justify-between">
                    <div>
                        {!! $row->{$field} !!}
                    </div>
                    <x-livewire-powergrid::click-to-copy
                        :row="$row"
                        :field="$row->{$field}"
                        :label="$column->click_to_copy['label'] ?? null"
                        :enabled="$column->click_to_copy['enabled'] ?? false"/>
                </span>
                @endif
            </td>
        @endif
    @endforeach
</div>
