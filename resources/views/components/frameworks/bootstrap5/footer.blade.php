<div>
    @includeIf(data_get($setUp, 'footer.includeViewOnTop'))
    @if (filled(data_get($setUp, 'footer.perPage')) &&
            count(data_get($setUp, 'footer.perPageValues')) > 1 &&
            blank(data_get($setUp, 'footer.pagination')))
        <footer
                class="{{ theme_style($theme, 'footer.footer', 'mt-50 pb-1 w-100 align-items-end px-1 d-flex flex-wrap justify-content-sm-center justify-content-md-between') }}"
        >
            <div class="col-auto overflow-auto my-sm-2 my-md-0 ms-sm-0">
                @if (filled(data_get($setUp, 'footer.perPage')) && count(data_get($setUp, 'footer.perPageValues')) > 1)
                    <div class="d-flex flex-lg-row align-items-center">
                        <label class="w-auto">
                            <select
                                    wire:model.live="setUp.footer.perPage"
                                    class="form-select {{ theme_style($theme, 'footer.select') }}"
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
                        </label>
                        <small class="ms-2 text-muted">
                            {{ trans('livewire-powergrid::datatable.labels.results_per_page') }}
                        </small>
                    </div>
                @endif
            </div>
            <div class="col-auto overflow-auto mt-2 mt-sm-0">
                @if (method_exists($data, 'links'))
                    {!! $data->links(data_get($setUp, 'footer.pagination') ?: data_get($theme, 'root') . '.pagination', [
                        'recordCount' => data_get($setUp, 'footer.recordCount'),
                    ]) !!}
                @endif
            </div>
        </footer>
    @endif
    @includeIf(data_get($setUp, 'footer.includeViewOnBottom'))
</div>
