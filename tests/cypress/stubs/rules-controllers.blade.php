<div class="my-3 space-y-3">
    <x-select
            class="w-[180px]"
            label="Select Rule"
            :options="[
        ['name' => 'Rows',  'value' => 'rows'],
        ['name' => 'Buttons', 'value' => 'buttons'],
        ['name' => 'Checkbox', 'value' => 'checkbox'],
        ['name' => 'Radio', 'value' => 'radio'],
    ]"
            option-label="name"
            option-value="value"
            wire:model="ruleType"
    />

    <x-textarea data-cy="dynamic-rules" wire:model="dynamicRules"/>

    <x-button primary data-cy="apply-rules" x-on:click="$wire.set('applyRules', true)" label="Apply Rules"/>
</div>
