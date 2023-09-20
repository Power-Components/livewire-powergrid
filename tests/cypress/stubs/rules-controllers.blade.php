<div class="my-3 space-y-3">
    <x-select
            class="w-[180px]"
            label="Select Rule"
            :options="[
        ['name' => 'Rows',  'id' => 1],
        ['name' => 'Buttons', 'id' => 2],
        ['name' => 'Checkbox', 'id' => 3],
        ['name' => 'Radio', 'id' => 4],
    ]"
            option-label="name"
            option-value="id"
            wire:model="rule"
    />

    <x-textarea data-cy="dynamic-rules" wire:model="dynamicRules"/>

    <x-button primary data-cy="apply-rules" x-on:click="$wire.set('applyRules', true)" label="Apply Rules"/>
</div>
