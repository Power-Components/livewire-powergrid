@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:text-gray-300">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button type="button" wire:click="previousPage"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:text-gray-300">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage"
                        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:text-gray-300">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:text-gray-300">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            @if($recordCount === 'full')
                <div>
                    <p class="text-md text-gray-700 leading-5 mr-2 dark:text-gray-300">
                        {{ trans('livewire-powergrid::datatable.pagination.showing') }}
                        <span class="font-semibold">{{ $paginator->firstItem() }}</span>
                        {{ trans('livewire-powergrid::datatable.pagination.to') }}
                        <span class="font-semibold">{{ $paginator->lastItem() }}</span>
                        {{ trans('livewire-powergrid::datatable.pagination.of') }}
                        <span class="font-semibold">{{ $paginator->total() }}</span>
                        {{ trans('livewire-powergrid::datatable.pagination.results') }}
                    </p>
                </div>
            @elseif($recordCount === 'short')
                <div>
                    <p class="text-md text-gray-700 leading-5 mr-2 dark:text-gray-300">
                        <span class="font-semibold"> {{ $paginator->firstItem() }}</span>
                        -
                        <span class="font-semibold"> {{ $paginator->lastItem() }}</span>
                        |
                        <span class="font-semibold"> {{ $paginator->total() }}</span>

                    </p>
                </div>
            @elseif($recordCount === 'min')
                <div>
                    <p class="text-md text-gray-700 leading-5 mr-2 dark:text-gray-300">
                        <span class="font-semibold"> {{ $paginator->firstItem() }}</span>
                        -
                        <span class="font-semibold"> {{ $paginator->lastItem() }}</span>
                    </p>
                </div>
            @endif

            <div class="flex justify-between flex-1 sm:hidden">
                @if ($paginator->onFirstPage())
                    <span
                        class="relative inline-flex items-center px-3 py-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:text-gray-300">
                    {!! __('pagination.previous') !!}
                </span>
                @else
                    <button type="button" wire:click="previousPage"
                            class="relative inline-flex items-center px-3 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:text-gray-300">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage"
                            class="relative inline-flex items-center px-3 py-3 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150  dark:text-gray-300">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span
                        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:text-gray-300">
                    {!! __('pagination.next') !!}
                </span>
                @endif
            </div>

            @if ( ! $paginator->onFirstPage())
                {{-- First Page Link --}}
                <a
                    class="mx-1 px-2 py-1 border-1 border-gray-400 text-gray-600 text-center hover:text-gray-600 rounded  cursor-pointer dark:hover:bg-gray-300 dark:hover:text-gray-700  dark:text-gray-300"
                    wire:click="gotoPage(1)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                        <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg>
                </a>
                @if($paginator->currentPage() > 2)
                    {{-- Previous Page Link --}}
                    <a
                        class="mx-1 px-2 py-1 border-1 border-gray-400 text-gray-600 text-center hover:text-gray-600 rounded  cursor-pointer dark:hover:bg-gray-300 dark:hover:text-gray-700  dark:text-gray-300"
                        wire:click="previousPage"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z"/>
                        </svg>
                    </a>
                @endif
            @endif

        <!-- Pagination Elements -->
            @foreach ($elements as $element)
            <!-- Array Of Links -->
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                    <!--  Use three dots when current page is greater than 3.  -->
                        @if ($paginator->currentPage() > 3 && $page === 2)
                            <div class="text-gray-800 mx-1 dark:text-gray-300">
                                <span class="font-bold">.</span>
                                <span class="font-bold">.</span>
                                <span class="font-bold">.</span>
                            </div>
                        @endif

                    <!--  Show active page two pages before and after it.  -->
                        @if ($page == $paginator->currentPage())
                            <span
                                class="mx-1 px-2 py-1 border-1 border-gray-400 bg-gray-500 text-white text-center hover:bg-gray-600 hover:border-gray-800 rounded cursor-pointer dark:text-gray-300">{{ $page }}</span>
                        @elseif ($page === $paginator->currentPage() + 1 || $page === $paginator->currentPage() + 2 || $page === $paginator->currentPage() - 1 || $page === $paginator->currentPage() - 2)
                            <a class="mx-1 px-2 py-1 border-1 border-gray-400 text-gray-600 text-center hover:text-gray-600 rounded  cursor-pointer  dark:text-gray-300"
                               wire:click="gotoPage({{$page}})">{{ $page }}</a>
                        @endif

                    <!--  Use three dots when current page is away from end.  -->
                        @if ($paginator->currentPage() < $paginator->lastPage() - 2  && $page === $paginator->lastPage() - 1)
                            <div class="text-gray-600 mx-1 dark:text-gray-300">
                                <span>.</span>
                                <span>.</span>
                                <span>.</span>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                @if($paginator->lastPage() - $paginator->currentPage() >= 2)
                    <a class="mx-1 px-2 py-1 border-1 border-gray-400 bg-gray-500 text-white text-center hover:bg-gray-600 hover:border-gray-800 rounded cursor-pointer dark:text-gray-300"
                       wire:click="nextPage"
                       rel="next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"/>
                        </svg>
                    </a>
                @endif
                <a
                    class="mx-1 px-2 py-1 border-1 border-gray-400 bg-gray-500 text-white text-center hover:bg-gray-600 hover:border-gray-800 rounded cursor-pointer dark:text-gray-300"
                    wire:click="gotoPage({{ $paginator->lastPage() }})"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
                        <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </a>
            @endif
        </div>
    </nav>
@endif

