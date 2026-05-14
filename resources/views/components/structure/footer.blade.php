@props([
    '__partial' => null,
])

@php
    $__partial = $__partial ?? $this;
    $setUp = $__partial->setUp;
    $tableName = $__partial->tableName;
@endphp

<div
    class="pg-footer-container"
    wire:partial="pg-pagination-{{ $tableName }}"
    wire:key="pagination-{{ $tableName }}"
>
    @includeIf(data_get($setUp, 'footer.includeViewOnTop'), ['__partial' => $__partial])
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
            @if (method_exists($__partial->records, 'links'))
                {!! $__partial->records->links(data_get($setUp, 'footer.pagination') ?: theme_view('pagination'), [
                    'recordCount' => data_get($setUp, 'footer.recordCount'),
                    'perPage' => data_get($setUp, 'footer.perPage'),
                    'perPageValues' => data_get($setUp, 'footer.perPageValues'),
                ]) !!}
            @endif
        </div>
    </footer>
    @includeIf(data_get($setUp, 'footer.includeViewOnBottom'), ['__partial' => $__partial])
</div>
