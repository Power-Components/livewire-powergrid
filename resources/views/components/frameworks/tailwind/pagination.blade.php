<nav role="navigation" aria-label="Pagination Navigation" class="items-center justify-between sm:flex">
    <div class="items-center justify-between w-full sm:flex-1 sm:flex">
        @if($recordCount === 'full')
            <div>
                <div class="mr-2 leading-5 text-center text-gray-700 text-md dark:text-gray-300 sm:text-right">
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
            <div>
                <p class="mr-2 leading-5 text-center text-gray-700 text-md dark:text-gray-300">
                    <span class="font-semibold firstItem"> {{ $paginator->firstItem() }}</span>
                    -
                    <span class="font-semibold lastItem"> {{ $paginator->lastItem() }}</span>
                    |
                    <span class="font-semibold total"> {{ $paginator->total() }}</span>

                </p>
            </div>
        @elseif($recordCount === 'min')
            <div>
                <p class="mr-2 leading-5 text-center text-gray-700 text-md dark:text-gray-300">
                    <span class="font-semibold firstItem"> {{ $paginator->firstItem() }}</span>
                    -
                    <span class="font-semibold lastItem"> {{ $paginator->lastItem() }}</span>
                </p>
            </div>
        @endif

@if ($paginator->hasPages())
        <div class="flex justify-center mt-2 md:flex-none md:justify-end sm:mt-0">

        @if(!$paginator->onFirstPage())

        <a
            class="px-2 py-1 pt-2 m-1 text-center text-white bg-gray-500 border-gray-400 rounded cursor-pointer border-1 hover:bg-gray-600 hover:border-gray-800 dark:text-gray-300"
            wire:click="gotoPage(1)"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
            </svg>
        </a>

        <a class="px-2 py-1 pt-2 m-1 text-center text-white bg-gray-500 border-gray-400 rounded cursor-pointer border-1 hover:bg-gray-600 hover:border-gray-800 dark:text-gray-300"
            wire:click="previousPage"
            rel="next">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z"/>
                </svg>
            </a>

        @endif

        @foreach ($elements as $element)
            @if (is_array($element))

                @foreach ($element as $page => $url)
                    @if ($paginator->currentPage() > 3 && $page === 2)
                        <div class="mx-1 mt-1 text-gray-800 dark:text-gray-300">
                            <span class="font-bold">.</span>
                            <span class="font-bold">.</span>
                            <span class="font-bold">.</span>
                        </div>
                    @endif

                    @if ($page == $paginator->currentPage())
                        <span
                            class="px-2 py-1 m-1 text-center border-gray-400 rounded cursor-pointer border-1 dark:bg-gray-700 dark:text-white dark:text-gray-300">{{ $page }}</span>
                    @elseif ($page === $paginator->currentPage() + 1 || $page === $paginator->currentPage() + 2 || $page === $paginator->currentPage() - 1 || $page === $paginator->currentPage() - 2)
                        <a class="px-2 py-1 m-1 text-center text-white bg-gray-500 border-gray-400 rounded cursor-pointer border-1 hover:bg-gray-600 hover:border-gray-800 dark:text-gray-300"
                           wire:click="gotoPage({{$page}})">{{ $page }}</a>
                    @endif

                    @if ($paginator->currentPage() < $paginator->lastPage() - 2  && $page === $paginator->lastPage() - 1)
                        <div class="mx-1 mt-1 text-gray-600 dark:text-gray-300">
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
                <a class="px-2 py-1 pt-2 m-1 text-center text-white bg-gray-500 border-gray-400 rounded cursor-pointer border-1 hover:bg-gray-600 hover:border-gray-800 dark:text-gray-300"
                   wire:click="nextPage"
                   rel="next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"/>
                    </svg>
                </a>
            @endif
            <a
                class="px-2 py-1 pt-2 m-1 text-center text-white bg-gray-500 border-gray-400 rounded cursor-pointer border-1 hover:bg-gray-600 hover:border-gray-800 dark:text-gray-300"
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
