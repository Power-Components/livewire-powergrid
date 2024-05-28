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
                    'leading-5 text-center text-pg-primary-700 text-md dark:text-pg-primary-300 sm:text-right',
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
                    'leading-5 text-center text-pg-primary-700 text-md dark:text-pg-primary-300 sm:text-right',
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
                    'leading-5 text-center text-pg-primary-700 text-md dark:text-pg-primary-300 sm:text-right',
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
                <div class="flex justify-center mt-2 md:flex-none md:justify-end sm:mt-0">

                    @if (!$paginator->onFirstPage())
                        <a
                            class="cursor-pointer relative inline-flex items-center px-2 py-2 text-sm font-medium text-pg-primary-500 dark:text-pg-primary-300 bg-white dark:bg-pg-primary-600 border border-pg-primary-300 dark:border-transparent rounded-l-md leading-5 hover:text-pg-primary-400 focus:z-10 focus:outline-none focus:shadow-outline-blue active:bg-pg-primary-100 active:text-pg-primary-500 transition ease-in-out duration-150"
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
                            class="cursor-pointer relative inline-flex items-center px-2 py-2 text-sm font-medium text-pg-primary-500 dark:text-pg-primary-300 bg-white dark:bg-pg-primary-600 border border-pg-primary-300 dark:border-transparent leading-5 hover:text-pg-primary-400 focus:z-10 focus:outline-none focus:shadow-outline-blue active:bg-pg-primary-100 active:text-pg-primary-500 transition ease-in-out duration-150"
                            wire:click="previousPage"
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
                                    <span
                                        class="select-none relative z-10 inline-flex items-center px-3 py-2 -ml-px text-sm font-bold text-primary-700 dark:text-pg-primary-300 bg-primary-100 dark:bg-pg-primary-700 border border-primary-300 dark:border-transparent cursor-default select-none"
                                    >{{ $page }}</span>
                                @elseif (
                                    $page === $paginator->currentPage() + 1 ||
                                        $page === $paginator->currentPage() + 2 ||
                                        $page === $paginator->currentPage() - 1 ||
                                        $page === $paginator->currentPage() - 2)
                                    <a
                                        class="select-none cursor-pointer relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-pg-primary-600 dark:text-pg-primary-400 bg-white dark:bg-pg-primary-600 border border-pg-primary-300 dark:border-transparent leading-5 hover:text-pg-primary-500 focus:z-10 focus:outline-none focus:shadow-outline-blue active:bg-pg-primary-100 active:text-pg-primary-700 transition ease-in-out duration-150"
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    >{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        @if ($paginator->lastPage() - $paginator->currentPage() >= 2)
                            <a
                                class="select-none cursor-pointer relative inline-flex items-center px-2 py-2 text-sm font-medium text-pg-primary-500 dark:text-pg-primary-300 bg-white dark:bg-pg-primary-600 border border-pg-primary-300 dark:border-transparent leading-5 hover:text-pg-primary-400 focus:z-10 focus:outline-none focus:shadow-outline-blue active:bg-pg-primary-100 active:text-pg-primary-500 transition ease-in-out duration-150"
                                wire:click="nextPage"
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
                        @endif
                        <a
                            class="select-none cursor-pointer cursor-pointer relative inline-flex items-center px-2 py-2 text-sm font-medium text-pg-primary-500 dark:text-pg-primary-300 bg-white dark:bg-pg-primary-600 border border-pg-primary-300 dark:border-transparent rounded-r-md leading-5 hover:text-pg-primary-400 focus:z-10 focus:outline-none focus:shadow-outline-blue active:bg-pg-primary-100 active:text-pg-primary-500 transition ease-in-out duration-150"
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
                            </svg></a>
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
                                    class="focus:ring-offset-white focus:shadow-outline group inline-flex items-center justify-center gap-x-2 border outline-none transition-all duration-200 ease-in-out hover:shadow-sm focus:border-transparent focus:ring-2 disabled:cursor-not-allowed disabled:opacity-80 text-md font-semibold px-4 py-2 text-pg-primary-500 bg-pg-primary-50 ring-0 ring-inset ring-pg-primary-300 hover:bg-pg-primary-100 dark:bg-pg-primary-800 dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-900 dark:text-pg-primary-300 focus-visible:outline-offset-0 rounded-md"
                                >
                                    @lang('Previous')
                                </button>
                            @else
                                @if (method_exists($paginator, 'getCursorName'))
                                    <button
                                        wire:click="setPage('{{ $paginator->previousCursor()->encode() }}','{{ $paginator->getCursorName() }}')"
                                        wire:loading.attr="disabled"
                                        class="select-none p-2 m-1 text-center text-white bg-pg-primary-600 border-pg-primary-400 rounded cursor-pointer border-1 hover:bg-pg-primary-600 hover:border-pg-primary-800 dark:text-pg-primary-300"
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
                                        class="select-none focus:ring-offset-white focus:shadow-outline group inline-flex items-center justify-center gap-x-2 border outline-none transition-all duration-200 ease-in-out hover:shadow-sm focus:border-transparent focus:ring-2 disabled:cursor-not-allowed disabled:opacity-80 text-md font-semibold px-4 py-2 text-pg-primary-500 bg-pg-primary-50 ring-0 ring-inset ring-pg-primary-300 hover:bg-pg-primary-100 dark:bg-pg-primary-800 dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-900 dark:text-pg-primary-300 focus-visible:outline-offset-0 rounded-md"
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
                                        class="select-none p-2 m-1 text-center text-white bg-pg-primary-600 border-pg-primary-400 rounded cursor-pointer border-1 hover:bg-pg-primary-600 hover:border-pg-primary-800 dark:text-pg-primary-300"
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
                                        class="select-none focus:ring-offset-white focus:shadow-outline group inline-flex items-center justify-center gap-x-2 border outline-none transition-all duration-200 ease-in-out hover:shadow-sm focus:border-transparent focus:ring-2 disabled:cursor-not-allowed disabled:opacity-80 text-md font-semibold px-4 py-2 text-pg-primary-500 bg-pg-primary-50 ring-0 ring-inset ring-pg-primary-300 hover:bg-pg-primary-100 dark:bg-pg-primary-800 dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-900 dark:text-pg-primary-300 focus-visible:outline-offset-0 rounded-md"
                                    >
                                        @lang('Next')
                                    </button>
                                @endif
                            @else
                                <button
                                    disabled
                                    class="focus:ring-offset-white focus:shadow-outline group inline-flex items-center justify-center gap-x-2 border outline-none transition-all duration-200 ease-in-out hover:shadow-sm focus:border-transparent focus:ring-2 disabled:cursor-not-allowed disabled:opacity-80 text-md font-semibold px-4 py-2 text-pg-primary-500 bg-pg-primary-50 ring-0 ring-inset ring-pg-primary-300 hover:bg-pg-primary-100 dark:bg-pg-primary-800 dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-900 dark:text-pg-primary-300 focus-visible:outline-offset-0 rounded-md"
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
