@if($checkbox)
    <td class="checkbox-column">
        <div class="form-check">
            <label>
                <input wire:model="checkboxValues" class="form-check-input" type="checkbox"  value="{{ $row->{$checkboxAttribute} }}">
            </label>
        </div>
    </td>
@endif
