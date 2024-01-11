<div class="my-3 space-y-3 text-black">
    <div>
        <select class="w-[180px]" wire:model="ruleType">
            @foreach([
                ['name' => 'Rows',  'value' => 'rows'],
                    ['name' => 'Buttons', 'value' => 'buttons'],
                    ['name' => 'Checkbox', 'value' => 'checkbox'],
                    ['name' => 'Radio', 'value' => 'radio'],
                ] as $option)
                <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <textarea data-cy="dynamic-rules" wire:model="dynamicRules"></textarea>
    </div>

    <button data-cy="apply-rules" x-on:click="$wire.set('applyRules', true)">Apply Rules</button>
</div>
