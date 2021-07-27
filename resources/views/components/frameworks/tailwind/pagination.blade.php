@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="sm:flex items-center justify-between">
        <div class="sm:flex-1 sm:flex items-center justify-between w-full">
            @if($recordCount === 'full')
                <div>
                    <p class="text-md text-gray-700 leading-5 mr-2 dark:text-gray-300 text-center sm:text-right">
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
                    <p class="text-md text-gray-700 leading-5 mr-2 dark:text-gray-300 text-center">
                        <span class="font-semibold"> {{ $paginator->firstItem() }}</span>
                        -
                        <span class="font-semibold"> {{ $paginator->lastItem() }}</span>
                        |
                        <span class="font-semibold"> {{ $paginator->total() }}</span>

                    </p>
                </div>
            @elseif($recordCount === 'min')
                <div>
                    <p class="text-md text-gray-700 leading-5 mr-2 dark:text-gray-300 text-center">
                        <span class="font-semibold"> {{ $paginator->firstItem() }}</span>
                        -
                        <span class="font-semibold"> {{ $paginator->lastItem() }}</span>
                    </p>
                </div>
            @endif

            <div class="flex justify-center md:flex-none md:justify-end mt-2 sm:mt-0">

            @foreach ($elements as $element)
                @if (is_array($element))

                    @foreach ($element as $page => $url)
                        @if ($paginator->currentPage() > 3 && $page === 2)
                            <div class="text-gray-800 mx-1 dark:text-gray-300 mt-1">
                                <span class="font-bold">.</span>
                                <span class="font-bold">.</span>
                                <span class="font-bold">.</span>
                            </div>
                        @endif

                        @if ($page == $paginator->currentPage())
                            <span
                                class="m-1 px-2 py-1 border-1 border-gray-400 dark:bg-gray-700 dark:text-white text-center rounded cursor-pointer dark:text-gray-300">{{ $page }}</span>
                        @elseif ($page === $paginator->currentPage() + 1 || $page === $paginator->currentPage() + 2 || $page === $paginator->currentPage() - 1 || $page === $paginator->currentPage() - 2)
                            <a class="m-1 px-2 py-1 border-1 border-gray-400 bg-gray-500 text-white text-center hover:bg-gray-600 hover:border-gray-800 rounded cursor-pointer dark:text-gray-300"
                               wire:click="gotoPage({{$page}})">{{ $page }}</a>
                        @endif

                        @if ($paginator->currentPage() < $paginator->lastPage() - 2  && $page === $paginator->lastPage() - 1)
                            <div class="text-gray-600 mx-1 dark:text-gray-300 mt-1">
                                <span>.</span>
                                <span>.</span>
                                <span>.</span>
                            </div>
                        @endif
                    @endforeach

                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                @if($paginator->lastPage() - $paginator->currentPage() >= 2)
                    <a class="m-1 px-2 pt-2 py-1 border-1 border-gray-400 bg-gray-500 text-white text-center hover:bg-gray-600 hover:border-gray-800 rounded cursor-pointer dark:text-gray-300"
                       wire:click="nextPage"
                       rel="next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"/>
                        </svg>
                    </a>
                @endif
                <a
                    class="m-1 px-2 pt-2 py-1 border-1 border-gray-400 bg-gray-500 text-white text-center hover:bg-gray-600 hover:border-gray-800 rounded cursor-pointer dark:text-gray-300"
                    wire:click="gotoPage({{ $paginator->lastPage() }})"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
                        <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </a>
            @endif
            </div>
        </div>
    </nav>
@endif

