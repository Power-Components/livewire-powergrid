<div class="pg-footer-container">
    @includeIf(data_get($setUp, 'footer.includeViewOnTop'))
    <footer
        id="pg-footer"
        class="pg-footer {{ theme('footer.layout.container') }}"
    >
        @if (filled(data_get($setUp, 'footer.perPage')) &&
                count(data_get($setUp, 'footer.perPageValues')) > 1 &&
                blank(data_get($setUp, 'footer.pagination')))
            <div class="pg-per-page {{ theme('footer.layout.per_page_container') }}">
                <select
                    wire:model.live="setUp.footer.perPage"
                    class="pg-per-page-select {{ theme('footer.layout.select') }}"
                >
                    @foreach (data_get($setUp, 'footer.perPageValues') as $value)
                        <option value="{{ $value }}">
                            {{ $value == 0 ? trans('livewire-powergrid::datatable.labels.all') : $value }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="pg-pagination {{ theme('footer.pagination.container') }}">
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
