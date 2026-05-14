@props([
    '__partial' => null,
])

@php
    $__partial = $__partial ?? $this;
    $setUp = $__partial->setUp;
    $tableName = $__partial->tableName;
@endphp

<div
    wire:partial="pg-pagination-{{ $tableName }}"
    wire:key="pagination-{{ $tableName }}"
>
    @includeIf(data_get($setUp, 'footer.includeViewOnTop'), ['__partial' => $__partial])
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
                <div class="relative">
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
                </div>
                <div
                    class="pl-4 hidden sm:block md:block lg:block w-full"
                    style="padding-top: 6px;"
                >
                </div>
            </div>
        @endif

        <div>
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
