@if (data_get($setUp, 'header.softDeletes'))
    @php($element = ($__partial ?? $this)->headerElement('softDeletes'))
    <div
        x-data="pgDropdown"
        @click.outside="close()"
        class="{{ theme('header.soft_deletes.wrapper') }}"
    >
        <button
            @click.prevent="toggle()"
            title="{{ $element['title'] }}"
            aria-label="{{ $element['title'] }}"
            class="{{ theme('header.soft_deletes.button', theme('header.layout.actions')) }}"
        >
            <div class="flex items-center">
                {!! $element['iconHtml'] !!}
                @if ($element['showLabel'])
                    <span class="{{ theme('header.soft_deletes.label') }}">{{ $element['title'] }}</span>
                @endif
            </div>
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition:enter="transform duration-200"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transform duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="{{ theme('header.soft_deletes.menu') }}"
        >

            <div
                x-on:click="dispatchClose('pg:softDeletes-{{ $tableName }}', 'softDeletes', '')"
                class="{{ theme('header.soft_deletes.menu_item') }}"
            >
                @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
            </div>
            <div
                x-on:click="dispatchClose('pg:softDeletes-{{ $tableName }}', 'softDeletes', 'withTrashed')"
                class="{{ theme('header.soft_deletes.menu_item') }}"
            >
                @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
            </div>
            <div
                x-on:click="dispatchClose('pg:softDeletes-{{ $tableName }}', 'softDeletes', 'onlyTrashed')"
                class="{{ theme('header.soft_deletes.menu_item') }}"
            >
                @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
            </div>

        </div>
    </div>
@endif
