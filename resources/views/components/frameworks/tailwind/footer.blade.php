<div>
    @includeIf(data_get($setUp, 'footer.includeViewOnTop'))
    <div @class([
        'justify-between' => data_get($setUp, 'footer.perPage'),
        'justify-end'  => blank(data_get($setUp, 'footer.perPage')),
        'md:flex md:flex-row w-full items-center pt-3 bg-white overflow-y-auto pl-2 pr-2 pb-1 relative
         dark:bg-slate-700' => blank(data_get($setUp, 'footer.pagination')),
])>
        @if(data_get($setUp, 'footer.perPage') && count(data_get($setUp, 'footer.perPageValues')) > 1 && blank(data_get($setUp, 'footer.pagination')))
            <div class="flex flex-row justify-center md:justify-start mb-2 md:mb-0">
                <div class="relative h-10">
                    <select wire:model.lazy="setUp.footer.perPage"
                            class="block appearance-none bg-slate-50 border border-slate-300 text-slate-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-slate-500  dark:bg-slate-600 dark:text-slate-200 dark:placeholder-slate-200 dark:border-slate-500">
                        @foreach(data_get($setUp, 'footer.perPageValues') as $value)
                            <option value="{{$value}}">
                                @if($value == 0)
                                    {{ trans('livewire-powergrid::datatable.labels.all') }}
                                @else
                                    {{ $value }}
                                @endif
                            </option>
                        @endforeach
                    </select>

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700">
                        <x-livewire-powergrid::icons.down class="w-4 h-4"/>
                    </div>
                </div>
                <div class="pl-4 hidden sm:block md:block lg:block w-full"
                     style="padding-top: 6px;">
                </div>
            </div>
        @endif

        @if(filled($data))
            <div>
                @if(method_exists($data, 'links'))
                    {!! $data->links(data_get($setUp, 'footer.pagination') ?: powerGridThemeRoot().'.pagination', [
                            'recordCount' => data_get($setUp, 'footer.recordCount'),
                            'perPage' => data_get($setUp, 'footer.perPage'),
                            'perPageValues' => data_get($setUp, 'footer.perPageValues'),
                        ])
                    !!}
                @endif
            </div>
        @endif
    </div>
    @includeIf(data_get($setUp, 'footer.includeViewOnBottom'))
</div>

