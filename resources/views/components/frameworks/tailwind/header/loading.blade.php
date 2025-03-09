<div class="hidden lg:!block">
    <div
        wire:loading
        wire:target.except="toggleDetail"
        class="mt-2 hidden"
    >
        <x-livewire-powergrid::icons.loading
            class="text-pg-primary-300 dark:text-pg-primary-400 h-5 w-5 animate-spin"
        />
    </div>
</div>
