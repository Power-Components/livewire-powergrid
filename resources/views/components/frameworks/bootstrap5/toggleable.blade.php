@if($column->toggleable['enabled'] === true)
    <div class="form-check form-switch">
        <label>
            <input onclick="toggle(event)" data-id="{{ $row->id }}" data-field="{{ $field }}" class="form-check-input" type="checkbox" @if($row->$field === 1) checked @endif>
        </label>
    </div>
@else
    <div class="text-center">
        @if($row->$field === 0)
            <div style="padding-top: 0.1em; padding-bottom: 0.1rem" class="badge bg-danger">
                {{ $column->toggleable['default'][1] }}
            </div>
        @else
            <div style="padding-top: 0.1em; padding-bottom: 0.1rem" class="badge bg-success">
                {{ $column->toggleable['default'][0] }}
            </div>
        @endif
    </div>
@endif
