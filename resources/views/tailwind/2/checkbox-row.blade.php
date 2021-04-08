@if($checkbox)
    <td class="px-6 py-4 whitespace-nowrap" style="width: 50px;">
        <input type="checkbox" wire:model="checkbox_values" value="{{ $row->{$checkbox_attribute} }}">
    </td>
@endif
