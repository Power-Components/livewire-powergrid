@if (data_get($setUp, 'header.softDeletes'))
    @php($element = ($__partial ?? $this)->headerElement('softDeletes'))
    <div class="dropdown" data-open-class="dropdown-open" :class="activeClass()" x-data="pgDropdown" x-on:keydown.esc="close()" x-on:click.outside="close()" wire:key="soft-deletes-dropdown-{{ $tableName }}">
        <div
            tabindex="0"
            role="button"
            class="{{ theme('header.soft_deletes.button', theme('header.layout.actions')) }}"
            x-on:click="toggle()"
            title="{{ $element['title'] }}"
            aria-label="{{ $element['title'] }}"
        >
            {!! $element['iconHtml'] !!}
            @if ($element['showLabel'])
                <span class="{{ theme('header.soft_deletes.label') }}">{{ $element['title'] }}</span>
            @endif
        </div>
        <ul tabindex="0" class="dropdown-content z-[50] menu p-2 shadow bg-base-100 rounded-box w-52 mt-2">
            <li>
                <a x-on:click="dispatchClose('pg:softDeletes-{{ $tableName }}', 'softDeletes', '')">
                    @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
                </a>
            </li>
            <li>
                <a x-on:click="dispatchClose('pg:softDeletes-{{ $tableName }}', 'softDeletes', 'withTrashed')">
                    @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
                </a>
            </li>
            <li>
                <a x-on:click="dispatchClose('pg:softDeletes-{{ $tableName }}', 'softDeletes', 'onlyTrashed')">
                    @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
                </a>
            </li>
        </ul>
    </div>
@endif
