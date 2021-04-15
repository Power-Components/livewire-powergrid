@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button type="button" wire:click="previousPage"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage"
                        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            @if($record_count === 'full')
                <div>
                    <p class="text-sm text-gray-700 leading-5 mr-2">
                        {{ trans('livewire-powergrid::datatable.pagination.showing') }}
                        <span class="font-semibold">{{ $paginator->firstItem() }}</span>
                        {{ trans('livewire-powergrid::datatable.pagination.to') }}
                        <span class="font-semibold">{{ $paginator->lastItem() }}</span>
                        {{ trans('livewire-powergrid::datatable.pagination.of') }}
                        <span class="font-semibold">{{ $paginator->total() }}</span>
                        {{ trans('livewire-powergrid::datatable.pagination.results') }}
                    </p>
                </div>
            @elseif($record_count === 'short')
                <div>
                    <p class="text-sm text-gray-700 leading-5">
                        <span class="font-semibold"> {{ $paginator->firstItem() }}</span>
                        -
                        <span class="font-semibold"> {{ $paginator->lastItem() }}</span>
                        |
                        <span class="font-semibold"> {{ $paginator->total() }}</span>

                    </p>
                </div>
            @elseif($record_count === 'min')
                <div>
                    <p class="text-sm text-gray-700 leading-5">
                        <span class="font-semibold"> {{ $paginator->firstItem() }}</span>
                        -
                        <span class="font-semibold"> {{ $paginator->lastItem() }}</span>
                    </p>
                </div>
            @endif

            <div class="flex justify-between flex-1 sm:hidden">
                @if ($paginator->onFirstPage())
                    <span
                        class="relative inline-flex items-center px-3 py-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
                @else
                    <button type="button" wire:click="previousPage"
                            class="relative inline-flex items-center px-3 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage"
                            class="relative inline-flex items-center px-3 py-3 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span
                        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.next') !!}
                </span>
                @endif
            </div>

            @if ( ! $paginator->onFirstPage())
                {{-- First Page Link --}}
                <a
                    class="mx-1 px-2 py-1 bg-gray-400 border-1 border-gray-600 text-white font-medium text-center hover:bg-gray-600 hover:border-gray-400 rounded  cursor-pointer"
                    wire:click="gotoPage(1)"
                >
                    <<
                </a>
                @if($paginator->currentPage() > 2)
                    {{-- Previous Page Link --}}
                    <a
                        class="mx-1 px-2 py-1 bg-gray-400 border-1 border-gray-600 text-white font-medium text-center hover:bg-gray-600 hover:gray-gray-400 rounded  cursor-pointer"
                        wire:click="previousPage"
                    >
                        <
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
                            <div class="text-gray-800 mx-1">
                                <span class="font-bold">.</span>
                                <span class="font-bold">.</span>
                                <span class="font-bold">.</span>
                            </div>
                        @endif

                    <!--  Show active page two pages before and after it.  -->
                        @if ($page == $paginator->currentPage())
                            <span
                                class="mx-1 px-2 py-1 border-1 border-gray-400 bg-gray-400 text-white font-bold text-center hover:bg-gray-600 hover:border-gray-800 rounded  cursor-pointer">{{ $page }}</span>
                        @elseif ($page === $paginator->currentPage() + 1 || $page === $paginator->currentPage() + 2 || $page === $paginator->currentPage() - 1 || $page === $paginator->currentPage() - 2)
                            <a class="mx-1 px-2 py-1 border-1 border-gray-400 text-gray-400 font-bold text-center hover:text-gray-400 rounded  cursor-pointer"
                               wire:click="gotoPage({{$page}})">{{ $page }}</a>
                        @endif

                    <!--  Use three dots when current page is away from end.  -->
                        @if ($paginator->currentPage() < $paginator->lastPage() - 2  && $page === $paginator->lastPage() - 1)
                            <div class="text-gray-400 mx-1">
                                <span class="font-bold">.</span>
                                <span class="font-bold">.</span>
                                <span class="font-bold">.</span>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                @if($paginator->lastPage() - $paginator->currentPage() >= 2)
                    <a class="mx-1 px-2 py-1 bg-gray-400 border-1 border-gray-400 text-white font-bold text-center hover:bg-gray-600 hover:border-gray-400 rounded  cursor-pointer"
                       wire:click="nextPage"
                       rel="next">
                        >
                    </a>
                @endif
                <a
                    class="mx-1 px-2 py-1 bg-gray-400 border-1 border-gray-400 text-white font-bold text-center hover:bg-gray-600 hover:border-gray-400 rounded  cursor-pointer"
                    wire:click="gotoPage({{ $paginator->lastPage() }})"
                >
                    >>
                </a>
            @endif
        </div>
    </nav>
@endif

