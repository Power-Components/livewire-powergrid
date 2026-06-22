<div class="dropdown dropdown-end" :class="{ 'dropdown-open': open }" x-data="{ open: false }" x-on:keydown.esc="open = false" x-on:click.outside="open = false" wire:key="actions-dropdown-{{ $tableName }}">
    <div tabindex="0" role="button" class="btn btn-sm btn-outline" x-on:click="open = !open">
        <x-livewire-powergrid::icons.down class="w-4 h-4" />
        Actions
    </div>
    <ul tabindex="0" class="dropdown-content z-[50] menu p-2 shadow bg-base-100 rounded-box w-52 mt-2">
        @if (data_get($setUp, 'exportable'))
            <li>
                <div class="px-0 py-0 hover:bg-transparent">
                    {!! $this->renderPluginZone('header') !!}
                </div>
            </li>
        @endif

        @if (data_get($setUp, 'header.toggleColumns'))
            <li>
                <div class="px-0 py-0 hover:bg-transparent">
                    @include(theme_view('header.toggle-columns'))
                </div>
            </li>
        @endif

        @if (data_get($setUp, 'header.softDeletes'))
            <li>
                <div class="px-0 py-0 hover:bg-transparent">
                    @include(theme_view('header.soft-deletes'))
                </div>
            </li>
        @endif
    </ul>
</div>
