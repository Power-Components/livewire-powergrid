<div class="hidden lg:!block">
    <div
        wire:loading
        wire:target.except="toggleDetail"
        class="mt-2 hidden"
    >
        <x-livewire-powergrid::icons.loading
            class="text-zinc-300 dark:text-zinc-400 size-4 animate-spin"
        />
    </div>
</div>
