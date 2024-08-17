@props([
    'id' => null,
    'data' => null,
    'empty' => null,
    'theme' => null,
])
<div class="relative">
    <select>
        <option value="">{{ $empty }}</option>
        @foreach ($data as $value => $label)
            <option
                wire:key="{{ $field->key . $value }}"
                value="{{ $value }}"
            >{{ $label }}</option>
        @endforeach
    </select>
    <div>
        <x-livewire-powergrid::icons.down />
    </div>
</div>
