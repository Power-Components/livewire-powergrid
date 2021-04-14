@if($checkbox)
    <td class="checkbox-column">
        <div class="form-check">
            <label>
                <input wire:model="checkbox_values" class="form-check-input" type="checkbox"  value="{{ $row->{$checkbox_attribute} }}">
            </label>
        </div>
    </td>
@endif
