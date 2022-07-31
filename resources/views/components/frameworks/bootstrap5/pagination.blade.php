<div class="d-flex flex-column flex-lg-row align-items-sm-center justify-content-between">
    @if($recordCount === 'full')
        <small class="text-muted d-block mb-2 my-md-2 me-1">
            {{ trans('livewire-powergrid::datatable.pagination.showing') }}
            <span class="font-semibold">{{ $paginator->firstItem() }}</span>
            {{ trans('livewire-powergrid::datatable.pagination.to') }}
            <span class="font-semibold">{{ $paginator->lastItem() }}</span>
            {{ trans('livewire-powergrid::datatable.pagination.of') }}
            <span class="font-semibold">{{ $paginator->total() }}</span>
            {{ trans('livewire-powergrid::datatable.pagination.results') }}
        </small>
    @elseif($recordCount === 'short')
        <small class="text-muted d-block mb-2 my-md-2 me-1">
            <span class="font-semibold"> {{ $paginator->firstItem() }}</span>
            -
            <span class="font-semibold"> {{ $paginator->lastItem() }}</span>
            |
            <span class="font-semibold"> {{ $paginator->total() }}</span>
        </small>
    @elseif($recordCount === 'min')
        <small class="text-muted d-block mb-2 my-md-2 me-1">
            <span class="font-semibold"> {{ $paginator->firstItem() }}</span>
            -
            <span class="font-semibold"> {{ $paginator->lastItem() }}</span>
        </small>
    @endif

    @if ($paginator->hasPages() && $recordCount != 'min')
        <nav>
            <ul class="pagination mb-0 ms-0 ms-sm-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" class="page-link" wire:click="previousPage"
                                wire:loading.attr="disabled" rel="prev" aria-label="@lang('pagination.previous')">
                            &lsaquo;
                        </button>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span
                                class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" wire:key="paginator-page-{{ $page }}" aria-current="page">
                                    <span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item" wire:key="paginator-page-{{ $page }}">
                                    <button type="button" class="page-link"
                                            wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <button type="button" class="page-link" wire:click="nextPage"
                                wire:loading.attr="disabled" rel="next" aria-label="@lang('pagination.next')">&rsaquo;
                        </button>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="page-link" aria-hidden="true">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif

    @if ($paginator->hasPages() && $recordCount == 'min')
        <nav>
            <ul class="pagination mb-0 ms-0 ms-sm-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <button type="button" class="page-link"
                                rel="prev" aria-label="@lang('pagination.previous')">
                            &lsaquo;
                        </button>
                    </li>
                @else
                    @if(method_exists($paginator,'getCursorName'))
                        <li class="page-item">
                            <button type="button" class="page-link"
                                    wire:click="setPage('{{$paginator->previousCursor()->encode()}}','{{ $paginator->getCursorName() }}')"
                                    wire:loading.attr="disabled"
                                    rel="prev" aria-label="@lang('pagination.previous')">
                                &lsaquo;
                            </button>
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" class="page-link"
                                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                    wire:loading.attr="disabled"
                                    rel="prev" aria-label="@lang('pagination.previous')">
                                &lsaquo;
                            </button>
                        </li>
                    @endif
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    @if(method_exists($paginator,'getCursorName'))
                        <li class="page-item">
                            <button type="button" class="page-link"
                                    wire:click="setPage('{{$paginator->nextCursor()->encode()}}','{{ $paginator->getCursorName() }}')"
                                    wire:loading.attr="disabled" rel="next" aria-label="@lang('pagination.next')">&rsaquo;
                            </button>
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" class="page-link"
                                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                    wire:loading.attr="disabled" rel="next" aria-label="@lang('pagination.next')">&rsaquo;
                            </button>
                        </li>
                    @endif
                @else
                    <li class="page-item disabled">
                        <button type="button" class="page-link"
                                rel="next" aria-label="@lang('pagination.next')">&rsaquo;
                        </button>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>
