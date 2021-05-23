@if($checkbox)
    <td class="px-6 py-4 whitespace-nowrap" style="width: 50px;">
        <div wire:ignore.self>
            <label class="flex items-center space-x-3">
                <input class="form-checkbox h-4 w-4" type="checkbox" wire:model="checkboxValues"
                       value="{{ $row->{$checkboxAttribute} }}">
            </label>
        </div>
    </td>
@endif
