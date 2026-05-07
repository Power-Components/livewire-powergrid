<div
    class="{{ theme('pagination.wrapper') }}"
    wire:loading.class="blur-[2px]"
    wire:target="loadMore"
>
    @if($paginator->count() > 0)
        <div class="{{ theme('pagination.count_wrapper') }}">

            {{-- Record count --}}
            @if ($recordCount === 'full')
                <div @class(['mr-3' => $paginator->hasPages()])>
                    <p @class(['mr-2' => $paginator->hasPages(), theme('pagination.count_text')])>
                        {{ trans('livewire-powergrid::datatable.pagination.showing') }}
                        <span class="{{ theme('pagination.count_value') }} firstItem">{{ $paginator->firstItem() }}</span>
                        {{ trans('livewire-powergrid::datatable.pagination.to') }}
                        <span class="{{ theme('pagination.count_value') }} lastItem">{{ $paginator->lastItem() }}</span>
                        {{ trans('livewire-powergrid::datatable.pagination.of') }}
                        <span class="{{ theme('pagination.count_value') }} total">{{ $paginator->total() }}</span>
                        {{ trans('livewire-powergrid::datatable.pagination.results') }}
                    </p>
                </div>
            @elseif($recordCount === 'short')
                <div @class(['mr-3' => $paginator->hasPages()])>
                    <p @class(['mr-2' => $paginator->hasPages(), theme('pagination.count_text')])>
                        <span class="{{ theme('pagination.count_value') }} firstItem">{{ $paginator->firstItem() }}</span>
                        –
                        <span class="{{ theme('pagination.count_value') }} lastItem">{{ $paginator->lastItem() }}</span>
                        /
                        <span class="{{ theme('pagination.count_value') }} total">{{ $paginator->total() }}</span>
                    </p>
                </div>
            @elseif($recordCount === 'min')
                <div @class(['mr-3' => $paginator->hasPages()])>
                    <p @class(['mr-2' => $paginator->hasPages(), theme('pagination.count_text')])>
                        <span class="{{ theme('pagination.count_value') }} firstItem">{{ $paginator->firstItem() }}</span>
                        –
                        <span class="{{ theme('pagination.count_value') }} lastItem">{{ $paginator->lastItem() }}</span>
                    </p>
                </div>
            @endif

            {{-- Full pagination --}}
            @if ($paginator->hasPages() && !in_array($recordCount, ['min', 'short']))
                <nav role="navigation" aria-label="Pagination Navigation" class="{{ theme('pagination.nav') }}">
                    <div class="{{ theme('pagination.nav_buttons') }}">

                        @if (!$paginator->onFirstPage())
                            <button
                                wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')"
                                class="{{ theme('pagination.button') }}"
                                aria-label="First page"
                            >
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                                </svg>
                            </button>

                            <button
                                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                class="{{ theme('pagination.button') }}"
                                rel="prev"
                                aria-label="Previous page"
                            >
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                </svg>
                            </button>
                        @endif

                        @foreach ($elements as $element)
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span class="{{ theme('pagination.page_active') }}">{{ $page }}</span>
                                    @elseif (
                                        $page === $paginator->currentPage() + 1 ||
                                        $page === $paginator->currentPage() + 2 ||
                                        $page === $paginator->currentPage() - 1 ||
                                        $page === $paginator->currentPage() - 2
                                    )
                                        <button
                                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                            class="{{ theme('pagination.page_inactive') }}"
                                        >{{ $page }}</button>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        @if ($paginator->hasMorePages())
                            <button
                                @class([
                                    theme('pagination.button'),
                                    'hidden' => $paginator->lastPage() - $paginator->currentPage() < 2,
                                ])
                                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                rel="next"
                                aria-label="Next page"
                            >
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>

                            <button
                                wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')"
                                class="{{ theme('pagination.button') }}"
                                aria-label="Last page"
                            >
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        @endif

                    </div>
                </nav>
            @endif

            {{-- Simple prev/next for min/short modes --}}
            <div>
                @if ($paginator->hasPages() && in_array($recordCount, ['min', 'short']))
                    <nav role="navigation" aria-label="Pagination Navigation" class="{{ theme('pagination.nav') }}">
                        <div class="{{ theme('pagination.nav_buttons_simple') }}">

                            @if ($paginator->onFirstPage())
                                <button disabled class="{{ theme('pagination.button_disabled') }}">
                                    {{ trans('livewire-powergrid::datatable.pagination.previous') }}
                                </button>
                            @else
                                @if (method_exists($paginator, 'getCursorName'))
                                    <button
                                        wire:click="setPage('{{ $paginator->previousCursor()->encode() }}','{{ $paginator->getCursorName() }}')"
                                        wire:loading.attr="disabled"
                                        class="{{ theme('pagination.button') }}"
                                    >
                                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                        </svg>
                                    </button>
                                @else
                                    <button
                                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                        wire:loading.attr="disabled"
                                        class="{{ theme('pagination.button_text') }}"
                                    >
                                        {{ trans('livewire-powergrid::datatable.pagination.previous') }}
                                    </button>
                                @endif
                            @endif

                            @if ($paginator->hasMorePages())
                                @if (method_exists($paginator, 'getCursorName'))
                                    <button
                                        wire:click="setPage('{{ $paginator->nextCursor()->encode() }}','{{ $paginator->getCursorName() }}')"
                                        wire:loading.attr="disabled"
                                        class="{{ theme('pagination.button') }}"
                                    >
                                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </button>
                                @else
                                    <button
                                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                        wire:loading.attr="disabled"
                                        class="{{ theme('pagination.button_text') }}"
                                    >
                                        {{ trans('livewire-powergrid::datatable.pagination.next') }}
                                    </button>
                                @endif
                            @else
                                <button disabled class="{{ theme('pagination.button_disabled') }}">
                                    {{ trans('livewire-powergrid::datatable.pagination.next') }}
                                </button>
                            @endif

                        </div>
                    </nav>
                @endif
            </div>

        </div>
    @endif
</div>
