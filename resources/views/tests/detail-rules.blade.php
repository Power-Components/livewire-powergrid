<div class="p-2 bg-white border border-pg-primary-200">
    <div>Id {{ $id }}</div>
    <div>Options @json($options)</div>

    <div class="flex justify-end">
        <button
            wire:click.prevent="toggleDetail('{{ $id }}')"
            class="p-1 text-xs bg-red-600 text-white rounded-lg"
        >Close</button>
    </div>
</div>
