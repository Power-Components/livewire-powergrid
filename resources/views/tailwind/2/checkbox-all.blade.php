@if($checkbox)
    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
        <label class="flex items-center space-x-3">
            <input class="form-checkbox h-4 w-4" type="checkbox" wire:click="selectCheckboxAll()" wire:model.lazy="checkboxAll">
        </label>
    </th>
@endif
