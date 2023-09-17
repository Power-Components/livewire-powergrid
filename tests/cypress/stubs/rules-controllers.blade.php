<div class="my-3 grid-cols-3">
    <x-textarea data-cy="dynamic-rules" wire:model="dynamicRules"/>

    <x-button primary data-cy="apply-rules" x-on:click="$wire.set('applyRules', true)" label="Apply Rules"/>
</div>
