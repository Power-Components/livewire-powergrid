<div class="form-check form-switch">
    <label>
        <input onclick="toggle(event);" data-id="{{ $row->id }}" data-field="{{ $field }}" class="form-check-input" type="checkbox" @if($row->$field === 1) checked @endif>
    </label>
</div>
