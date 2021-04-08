@if($checkbox)
    <th class="checkbox-column">
        <label class="new-control new-checkbox checkbox-primary" id="new-control"
               style="height: 18px; margin: 0 auto;">
            <input type="checkbox" class="new-control-input" wire:model="checkbox_all">
            <span class="new-control-indicator"></span>
        </label>
    </th>
@endif
