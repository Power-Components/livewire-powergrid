@if($checkbox)
    <td class="checkbox-column">
        <label class="new-control new-checkbox checkbox-primary"
               style="height: 18px; margin: 0 auto;">
            <input type="checkbox" wire:model="checkbox_values"
                   class="new-control-input todochkbox" id="todo-1"
                   value="{{ $row->{$checkbox_attribute} }}">
            <span class="new-control-indicator"></span>
        </label>
    </td>
@endif
