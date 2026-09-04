@props([
    'primaryKey' => null,
    'row' => null,
    'field' => null,
    'currentTable' => null,
    'tableName' => null,
    'showErrorBag' => null,
    'editable' => null,
])

@php
    $resolveContent = function (
        string $currentTable,
        string $field,
        \Illuminate\Database\Eloquent\Model|\stdClass $row,
    ): ?string {
        $currentField = $field;
        $replace = fn($content) => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content ?? '');

        /** @codeCoverageIgnore */
        if (str_contains($currentField, '.')) {
            $data = \Illuminate\Support\Str::of($field)->explode('.');
            $table = $data->get(0);
            $field = $data->get(1);

            if ($table === $currentTable) {
                return $replace($row->{$field});
            }

            return $replace($row->{$table}->{$field});
        }

        return $replace($row->{$field});
    };

    $fallback = html_entity_decode(strval(data_get($editable, 'fallback')), ENT_QUOTES, 'utf-8');
    $value = html_entity_decode(strval($resolveContent($currentTable, $field, $row)), ENT_QUOTES, 'utf-8');

    $content = !empty($value) || $value == '0' ? $value : $fallback;

    $params = [
        'theme' => theme('name'),
        'tableName' => $tableName,
        'id' => data_get($row, $primaryKey),
        'dataField' => $field,
        'content' => $content,
        'fallback' => $fallback,
        'inputClass' => theme('editable.input'),
        'saveOnMouseOut' => (bool) data_get($editable, 'saveOnMouseOut'),
    ];
@endphp

<div
    wire:key="editable-{{ uniqid() }}"
    x-cloak
    x-data="pgEditable"
    data-pg-params="{{ json_encode($params) }}"
    style="width: 100% !important; height: 100% !important;"
>
    <div
        class="{{ theme('editable.clickable') }}"
        x-show="notEditing()"
        x-on:click="startEdit()"
        :id="clickableId()"
        style="cursor: pointer; width: 100%; height: 100%;"
    >
        <span
            style="border-bottom: dotted 1px;"
            x-text="content"
        ></span>
    </div>

    <template x-if="showEditable">
        <div
            x-ref="editable"
            x-text="content"
            :value="content"
            :placeholder="content"
            contenteditable
            :class="singleLineClass()"
            @if ((bool) data_get($editable, 'saveOnMouseOut')) x-on:mousedown.outside="save()" @endif
            x-on:keydown.enter="save()"
            :id="editableId()"
            x-on:keydown.esc="cancel()"
        >
        </div>
    </template>

    @if ($showErrorBag)
        @error($field . '.' . $row->{$primaryKey})
            <div
                x-ref="error"
                class="{{ theme('editable.error') }}"
            >
                {{ str($message)->replace($field . '.' . $row->{$primaryKey}, $field) }}
            </div>
        @enderror
    @endif
</div>
