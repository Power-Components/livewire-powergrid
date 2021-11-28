@props([
    'theme' => null,
    'row' => null,
    'primaryKey' => null,
    'columns' => null,
    'currentTable' => null,
])

@foreach($columns as $column)
    @php
        $content = $row->{$column->field};
        $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
    @endphp
    @if($column->hidden === false)
        <td class="{{ $theme->table->tdBodyClass . ' '.$column->bodyClass ?? '' }}"
            style=" {{ $theme->table->tdBodyStyle . ' '.$column->bodyStyle ?? '' }}"
        >
            @if($column->editable === true)
                <span class="{{ $theme->editable->spanClass }}">
                        <x-livewire-powergrid::editable
                            :primaryKey="$primaryKey"
                            :currentTable="$currentTable"
                            :row="$row"
                            :theme="$theme->editable"
                            :field="$column->dataField != '' ? $column->dataField : $column->field"/>

                        <x-livewire-powergrid::click-to-copy
                            :row="$row"
                            :field="$content"
                            :label="$column->click_to_copy['label'] ?? null"
                            :enabled="$column->click_to_copy['enabled'] ?? false"/>
                    </span>

            @elseif(count($column->toggleable) > 0)
                @include($theme->toggleable->view)
            @else
                <span class="flex justify-between">
                    <div>
                        {!! $content !!}
                    </div>
                    <x-livewire-powergrid::click-to-copy
                        :row="$row"
                        :field="$content"
                        :label="data_get($column->clickToCopy, 'label') ?? null"
                        :enabled="data_get($column->clickToCopy, 'enabled') ?? false"/>
                </span>
            @endif
        </td>
    @endif
@endforeach

