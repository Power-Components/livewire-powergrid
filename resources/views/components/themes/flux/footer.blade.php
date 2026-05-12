<div>
    @includeIf(data_get($setUp, 'footer.includeViewOnTop'))
    <footer
        id="pg-footer"
        @class([
            'justify-between' => filled(data_get($setUp, 'footer.perPage')),
            'justify-end' => blank(data_get($setUp, 'footer.perPage')),
            theme('footer.layout.container'),
        ])
    >
        @if (filled(data_get($setUp, 'footer.perPage')) &&
                count(data_get($setUp, 'footer.perPageValues')) > 1 &&
                blank(data_get($setUp, 'footer.pagination')))
            <div class="flex flex-row justify-center md:justify-start mb-2 md:mb-0">
                <div class="relative inline-flex items-center">
                    <select
                        wire:model.live="setUp.footer.perPage"
                        class="{{ theme('footer.layout.select') }}"
                    >
                        @foreach (data_get($setUp, 'footer.perPageValues') as $value)
                            <option value="{{ $value }}">
                                @if ($value == 0)
                                    {{ trans('livewire-powergrid::datatable.labels.all') }}
                                @else
                                    {{ $value }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-zinc-400 dark:text-zinc-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        @endif

        <div>
            @if (method_exists($this->records, 'links'))
                {!! $this->records->links(data_get($setUp, 'footer.pagination') ?: theme_view('pagination'), [
                    'recordCount' => data_get($setUp, 'footer.recordCount'),
                    'perPage' => data_get($setUp, 'footer.perPage'),
                    'perPageValues' => data_get($setUp, 'footer.perPageValues'),
                ]) !!}
            @endif
        </div>
    </footer>
    @includeIf(data_get($setUp, 'footer.includeViewOnBottom'))
</div>
