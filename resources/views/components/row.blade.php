@foreach ($columns as $column)
    @php
        $content = $row->{$column->field};
        $contentClassField = $column->contentClassField != '' ? $row->{$column->contentClassField} : '';
        $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
        $field = $column->dataField != '' ? $column->dataField : $column->field;
        $contentClass = array_key_exists($content, $column->contentClasses) ? $column->contentClasses[$content] : '';
    @endphp
    <td
        class="{{ $theme->table->tdBodyClass . ' ' . $column->bodyClass ?? '' }}"
        style="{{ $column->hidden === true ? 'display:none' : '' }}; {{ $theme->table->tdBodyStyle . ' ' . $column->bodyStyle ?? '' }}"
    >
        <div class="flex gap-2 w-full">
            <!-- Render Action -->
            @if (filled(data_get($row, 'actions')) && blank($column->field))
                @foreach(data_get($row, 'actions') as $key => $action)
                    <div wire:key="action-{{ $row->id }}-{{ $key }}">
                        {!! $action !!}
                    </div>
                @endforeach
            @endif
        </div>

{{--        <!-- Render Livewire Component Action -->--}}
{{--        @if (data_get($column, 'livewire.component'))--}}
{{--            <livewire:is--}}
{{--                key="lv-dy-{{ md5($row->id . data_get($setUp, 'footer.perPage') . $this->getPage()) }}"--}}
{{--                :component="data_get($column, 'livewire.component')"--}}
{{--                :field="$field"--}}
{{--                :row="$row"--}}
{{--                :params="data_get($column, 'livewire.params', [])"--}}
{{--            />--}}
{{--        @endif--}}

        @if (data_get($column->editable, 'hasPermission') && !str_contains($field, '.'))
            <span class="{{ $contentClassField . ' ' . $contentClass }}">
                @include($theme->editable->view, ['editable' => $column->editable])
            </span>
        @elseif(count($column->toggleable) > 0)
            @php
                $rules = $actionRulesClass->recoverFromAction('pg:rows', $row);
                $toggleableRules = collect(data_get($rules, 'showHideToggleable', []));
                $showToggleable = $toggleableRules->isEmpty() || $toggleableRules->last() == 'show';
            @endphp
            @include($theme->toggleable->view, ['tableName' => $tableName])
        @else
            <span class="{{ $contentClassField . ' ' . $contentClass }}">
                <div>{!! $column->index ? $rowIndex : $content !!}</div>
            </span>
        @endif
    </td>
@endforeach
