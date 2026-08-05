<div class="dropdown dropdown-end" data-open-class="dropdown-open" :class="activeClass()" x-data="pgDropdown" x-on:keydown.esc="close()" x-on:click.outside="close()" wire:key="actions-dropdown-{{ $tableName }}">
    <div tabindex="0" role="button" class="btn btn-sm btn-outline" x-on:click="toggle()">
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
