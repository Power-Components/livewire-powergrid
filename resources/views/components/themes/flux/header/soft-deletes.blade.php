@if (data_get($setUp, 'header.softDeletes'))
    @php($element = ($__partial ?? $this)->headerElement('softDeletes'))
    <div wire:key="soft-deletes-dropdown-{{ $tableName }}" class="{{ theme('header.soft_deletes.wrapper') }}">
        <flux:dropdown>
            <button
                type="button"
                class="{{ theme('header.soft_deletes.button', theme('header.layout.actions')) }}"
                title="{{ $element['title'] }}"
                aria-label="{{ $element['title'] }}"
            >
                {!! $element['iconHtml'] !!}
                @if ($element['showLabel'])
                    <span class="{{ theme('header.soft_deletes.label') }}">{{ $element['title'] }}</span>
                @endif
            </button>

            <flux:menu class="{{ theme('header.soft_deletes.menu') }}">
                <flux:menu.item
                    wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: ''})"
                >
                    @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
                </flux:menu.item>
                <flux:menu.item
                    wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'withTrashed'})"
                >
                    @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
                </flux:menu.item>
                <flux:menu.item
                    wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'onlyTrashed'})"
                >
                    @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
@endif
