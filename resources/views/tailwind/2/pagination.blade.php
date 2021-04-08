@if ($paginator->hasPages())
    <div class="flex items-end my-2">

        @if ( ! $paginator->onFirstPage())
            {{-- First Page Link --}}
            <a
                class="mx-1 px-2 py-1 bg-indigo-400 border-1 border-indigo-600 text-white font-medium text-center hover:bg-indigo-400 hover:border-indigo-400 rounded-lg  cursor-pointer"
                wire:click="gotoPage(1)"
            >
                <<
            </a>
            @if($paginator->currentPage() > 2)
                {{-- Previous Page Link --}}
                <a
                    class="mx-1 px-2 py-1 bg-indigo-400 border-1 border-indigo-600 text-white font-medium text-center hover:bg-indigo-400 hover:indigo-indigo-400 rounded-lg  cursor-pointer"
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
                        <div class="text-indigo-800 mx-1">
                            <span class="font-bold">.</span>
                            <span class="font-bold">.</span>
                            <span class="font-bold">.</span>
                        </div>
                    @endif

                <!--  Show active page two pages before and after it.  -->
                    @if ($page == $paginator->currentPage())
                        <span class="mx-1 px-2 py-1 border-1 border-indigo-400 bg-indigo-400 text-white font-bold text-center hover:bg-indigo-800 hover:border-indigo-800 rounded-lg  cursor-pointer">{{ $page }}</span>
                    @elseif ($page === $paginator->currentPage() + 1 || $page === $paginator->currentPage() + 2 || $page === $paginator->currentPage() - 1 || $page === $paginator->currentPage() - 2)
                        <a class="mx-1 px-2 py-1 border-1 border-indigo-400 text-indigo-400 font-bold text-center hover:text-indigo-400 rounded-lg  cursor-pointer" wire:click="gotoPage({{$page}})">{{ $page }}</a>
                    @endif

                <!--  Use three dots when current page is away from end.  -->
                    @if ($paginator->currentPage() < $paginator->lastPage() - 2  && $page === $paginator->lastPage() - 1)
                        <div class="text-indigo-400 mx-1">
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
                <a class="mx-1 px-2 py-1 bg-indigo-400 border-1 border-indigo-400 text-white font-bold text-center hover:bg-indigo-400 hover:border-indigo-400 rounded-lg  cursor-pointer"
                   wire:click="nextPage"
                   rel="next">
                    >
                </a>
            @endif
            <a
                class="mx-1 px-2 py-1 bg-indigo-400 border-1 border-indigo-400 text-white font-bold text-center hover:bg-indigo-400 hover:border-indigo-400 rounded-lg  cursor-pointer"
                wire:click="gotoPage({{ $paginator->lastPage() }})"
            >
                >>
            </a>
        @endif
    </div>
@endif
