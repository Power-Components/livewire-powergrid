@if($paginator->count() > 0)
    @if ($paginator->hasPages())
        <flux:pagination :paginator="$paginator" class="justify-end! gap-2! border-t-0! pt-0!" />
    @else
        @if($recordCount === 'full')
            <p class="{{ theme('pagination.count_text') }} ml-auto">
                {{ trans('livewire-powergrid::datatable.pagination.showing') }}
                <span class="{{ theme('pagination.count_value') }} firstItem">{{ $paginator->firstItem() }}</span>
                {{ trans('livewire-powergrid::datatable.pagination.to') }}
                <span class="{{ theme('pagination.count_value') }} lastItem">{{ $paginator->lastItem() }}</span>
                {{ trans('livewire-powergrid::datatable.pagination.of') }}
                <span class="{{ theme('pagination.count_value') }} total">{{ $paginator->total() }}</span>
                {{ trans('livewire-powergrid::datatable.pagination.results') }}
            </p>
        @elseif($recordCount === 'short')
            <p class="{{ theme('pagination.count_text') }} ml-auto">
                <span class="{{ theme('pagination.count_value') }} firstItem">{{ $paginator->firstItem() }}</span>
                –
                <span class="{{ theme('pagination.count_value') }} lastItem">{{ $paginator->lastItem() }}</span>
                /
                <span class="{{ theme('pagination.count_value') }} total">{{ $paginator->total() }}</span>
            </p>
        @elseif($recordCount === 'min')
            <p class="{{ theme('pagination.count_text') }} ml-auto">
                <span class="{{ theme('pagination.count_value') }} firstItem">{{ $paginator->firstItem() }}</span>
                –
                <span class="{{ theme('pagination.count_value') }} lastItem">{{ $paginator->lastItem() }}</span>
            </p>
        @endif
    @endif
@endif
