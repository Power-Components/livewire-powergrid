<div
    class="items-center justify-between sm:flex gap-2"
    wire:loading.class="blur-[2px]"
    wire:target="loadMore"
>
    <div class="items-center justify-between w-full sm:flex-1 sm:flex">
        @if ($recordCount === 'full')
            <div @class(['mr-3' => $paginator->hasPages()])>
                <div @class([
                    'mr-2' => $paginator->hasPages(),
                    'leading-5 text-center sm:text-right',
                ])>
                    {{ trans('livewire-powergrid::datatable.pagination.showing') }}
                    <span class="font-semibold firstItem">{{ $paginator->firstItem() }}</span>
                    {{ trans('livewire-powergrid::datatable.pagination.to') }}
                    <span class="font-semibold lastItem">{{ $paginator->lastItem() }}</span>
                    {{ trans('livewire-powergrid::datatable.pagination.of') }}
                    <span class="font-semibold total">{{ $paginator->total() }}</span>
                    {{ trans('livewire-powergrid::datatable.pagination.results') }}
                </div>
            </div>
        @elseif($recordCount === 'short')
            <div @class(['mr-3' => $paginator->hasPages()])>
                <p @class([
                    'mr-2' => $paginator->hasPages(),
                    'leading-5 text-center sm:text-right',
                ])>
                    <span class="font-semibold firstItem"> {{ $paginator->firstItem() }}</span>
                    -
                    <span class="font-semibold lastItem"> {{ $paginator->lastItem() }}</span>
                    |
                    <span class="font-semibold total"> {{ $paginator->total() }}</span>
                </p>
            </div>
        @elseif($recordCount === 'min')
            <div @class(['mr-3' => $paginator->hasPages()])>
                <p @class([
                    'mr-2' => $paginator->hasPages(),
                    'leading-5 text-center sm:text-right',
                ])>
                    <span class="font-semibold firstItem"> {{ $paginator->firstItem() }}</span>
                    -
                    <span class="font-semibold lastItem"> {{ $paginator->lastItem() }}</span>
                </p>
            </div>
        @endif

        @if ($paginator->hasPages() && !in_array($recordCount, ['min', 'short']))
            <nav
                role="navigation"
                aria-label="Pagination Navigation"
                class="items-center justify-between sm:flex"
            >
                <div class="flex join justify-center mt-2 md:flex-none md:justify-end sm:mt-0">

                    @if (!$paginator->onFirstPage())
                        <a
                            class="btn join-item"
                            wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')"
                        >
                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-5 h-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5"
                                />
                            </svg>
                        </a>

                        <a
                            class="btn join-item flex items-center"
                            wire:click="previousPage('{{ $paginator->getPageName() }}')"
                            rel="next"
                        >
                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-5 h-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 19.5 8.25 12l7.5-7.5"
                                />
                            </svg>

                        </a>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="btn join-item btn-primary">{{ $page }}</span>
                                @elseif (
                                    $page === $paginator->currentPage() + 1 ||
                                        $page === $paginator->currentPage() + 2 ||
                                        $page === $paginator->currentPage() - 1 ||
                                        $page === $paginator->currentPage() - 2)
                                    <a
                                        class="btn join-item"
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    >{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <a
                            @class([
                                'block' => $paginator->lastPage() - $paginator->currentPage() >= 2,
                                'hidden' => $paginator->lastPage() - $paginator->currentPage() < 2,
                                'btn join-item flex',
                            ])
                            wire:click="nextPage('{{ $paginator->getPageName() }}')"
                            rel="next"
                        >
                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-5 h-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m8.25 4.5 7.5 7.5-7.5 7.5"
                                />
                            </svg>
                        </a>
                        <a
                            class="btn join-item"
                            wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')"
                        >
                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-5 h-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5"
                                />
                            </svg>
                        </a>
                    @endif
                </div>
            </nav>
        @endif

        <div>
            @if ($paginator->hasPages() && in_array($recordCount, ['min', 'short']))
                <nav
                    role="navigation"
                    aria-label="Pagination Navigation"
                    class="items-center justify-between sm:flex"
                >
                    <div class="flex justify-center gap-2 md:flex-none md:justify-end sm:mt-0">
                        <span>
                            {{-- Previous Page Link Disabled --}}
                            @if ($paginator->onFirstPage())
                                <button
                                    disabled
                                    class="btn join-item"
                                >
                                    @lang('Previous')
                                </button>
                            @else
                                @if (method_exists($paginator, 'getCursorName'))
                                    <button
                                        wire:click="setPage('{{ $paginator->previousCursor()->encode() }}','{{ $paginator->getCursorName() }}')"
                                        wire:loading.attr="disabled"
                                        class="btn join-item"
                                    >
                                        <svg
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-5 h-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5"
                                            />
                                        </svg>

                                    </button>
                                @else
                                    <button
                                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                        wire:loading.attr="disabled"
                                        class="select-none btn join-item"
                                    >
                                        @lang('Previous')
                                    </button>
                                @endif
                            @endif
                        </span>

                        <span>
                            {{-- Next Page Link --}}
                            @if ($paginator->hasMorePages())
                                @if (method_exists($paginator, 'getCursorName'))
                                    <button
                                        wire:click="setPage('{{ $paginator->nextCursor()->encode() }}','{{ $paginator->getCursorName() }}')"
                                        wire:loading.attr="disabled"
                                        class="btn join-item"
                                    >
                                        <svg
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-5 h-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5"
                                            />
                                        </svg>

                                    </button>
                                @else
                                    <button
                                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                        wire:loading.attr="disabled"
                                        class="btn join-item"
                                    >
                                        @lang('Next')
                                    </button>
                                @endif
                            @else
                                <button
                                    disabled
                                    class="btn join-item"
                                >
                                    @lang('Next')
                                </button>
                            @endif
                        </span>
                    </div>
                </nav>
            @endif
        </div>
    </div>
</div>
