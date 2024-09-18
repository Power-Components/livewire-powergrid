<div class="dropdown">
    <button
        class="btn btn-secondary dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
    >
        <svg
            width="20"
            height="20"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
        </svg>
    </button>
    <ul x-data="{countChecked: @entangle('checkboxValues').live}" class="dropdown-menu">
        @if (in_array('xlsx', data_get($setUp, 'exportable.type')))
            <li class="d-flex">
                <div class="dropdown-item">
                    <span>@lang('XLSX')</span>

                    <a
                        class="text-muted small"
                        wire:click.prevent="exportToXLS"
                        href="#"
                    >
                        <span class="export-count text-muted small">({{ $total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </a>
                    @if ($checkbox)
                        /
                        <a
                            class="text-muted small"
                            wire:click.prevent="exportToXLS(true)"
                            href="#"
                        >
                            (<span class="text-muted small" x-text="countChecked.length"></span>) @lang('livewire-powergrid::datatable.labels.selected')
                        </a>
                    @endif
                </div>
            </li>
        @endif
        @if (in_array('csv', data_get($setUp, 'exportable.type')))
            <li class="d-flex">
                <div class="dropdown-item">
                    <span>@lang('Csv')</span>
                    <a
                        class="text-muted small"
                        wire:click.prevent="exportToCsv"
                        href="#"
                    >
                        <span class="export-count text-muted small">({{ $total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </a>
                    @if ($checkbox)
                        /
                        <a
                            class="text-muted small"
                            wire:click.prevent="exportToCsv(true)"
                            x-bind:disabled="countChecked.length === 0"
                            href="#"
                        >
                            (<span class="text-muted small" x-text="countChecked.length"></span>) @lang('livewire-powergrid::datatable.labels.selected')
                        </a>
                    @endif
                </div>
            </li>
        @endif
    </ul>
</div>
