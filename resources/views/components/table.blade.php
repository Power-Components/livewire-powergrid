@php
    use PowerComponents\LivewirePowerGrid\DataSource\DataTransformer;
    use PowerComponents\LivewirePowerGrid\PowerGridComponent;

    $dataTransformer = new DataTransformer($this);
    $tableIsLazy = !is_null(data_get($setUp, 'lazy'));
    $lazyConfig = data_get($setUp, 'lazy');
    $rowsPerChildren = data_get($lazyConfig, 'rowsPerChildren')

    /** @var PowerGridComponent $this */

@endphp
<x-livewire-powergrid::table-base
    :$readyToLoad
    :$tableName
    :$theme
    :lazy="$tableIsLazy"
>
    <x-slot:header>
        @include('livewire-powergrid::components.table.tr')
    </x-slot:header>

    <x-slot:loading>
        @include('livewire-powergrid::components.table.tr', ['loading' => true])
    </x-slot:loading>

    <x-slot:body>
        @includeWhen($this->hasColumnFilters, 'livewire-powergrid::components.inline-filters')

        @if (count($this->records) === 0)
            @include('livewire-powergrid::components.table.th-empty')
        @else
            @includeWhen($headerTotalColumn, 'livewire-powergrid::components.table-header')

            @if (empty($lazyConfig))

                @if (isset($setUp['detail']))
                    @foreach ($this->records as $row)
                        @php
                            $rowId = data_get($row, $this->realPrimaryKey);
                            $class = theme_style($theme, 'table.body.tr');
                        @endphp

                        <tbody
                            wire:key="tbody-{{ substr($rowId, 0, 6) }}"
                            class="{{ $class }}"
                        >
                            @include('livewire-powergrid::components.row', [
                                'rowIndex' => $loop->index + 1,
                            ])

                            @php
                                $hasDetailView = (bool) data_get(
                                    collect($row->__powergrid_rules)->where('apply', true)->last(),
                                    'detailView',
                                );

                                if ($hasDetailView) {
                                    $detailView = data_get($row->__powergrid_rules, '0.detailView');
                                    $rulesValues = data_get($row->__powergrid_rules, '0.options', []);
                                } else {
                                    $detailView = data_get($setUp, 'detail.view');
                                    $rulesValues = data_get($setUp, 'detail.options', []);
                                }
                            @endphp

                            @php
                                if ($row instanceof stdClass) {
                                    $row = collect($row);
                                }
                            @endphp

                            <livewire:powergrid-detail
                                key="powergrid-detail-{{ $rowId }}"
                                :view="$detailView"
                                :options="$rulesValues"
                                :row-id="$rowId"
                                tr-class="{{ $class }}"
                                :row="(object) $row->toArray()"
                                :collapse-others="data_get($setUp, 'detail.collapseOthers', false)"
                                :table-name="$tableName"
                            />
                        </tbody>

                        @includeWhen(isset($setUp['responsive']),
                            'livewire-powergrid::components.expand-container')
                    @endforeach
                @else
                    @foreach ($this->records as $row)
                        @php
                            $rowId = data_get($row, $this->realPrimaryKey);
                            $class = theme_style($theme, 'table.body.tr');
                        @endphp

                        <tr
                            wire:replace.self
                            x-data="pgRowAttributes({ rowId: @js($rowId), defaultClasses: @js($class), rules: @js($row->__powergrid_rules) })"
                            x-bind="getAttributes"
                        >
                            @include('livewire-powergrid::components.row', [
                                'rowIndex' => $loop->index + 1,
                            ])
                        </tr>

                        @includeWhen(isset($setUp['responsive']),
                            'livewire-powergrid::components.expand-container')
                    @endforeach
                @endif
            @else
                <div>
                    @foreach (range(0, data_get($lazyConfig, 'items')) as $item)
                        @php
                            $skip = $item * $rowsPerChildren;
                            $take = $rowsPerChildren;
                        @endphp

                        <livewire:lazy-child
                            key="{{ $this->getLazyKeys }}"
                            :parentId="$this->getId()"
                            :child-index="$item"
                            :primary-key="$primaryKey"
                            real-primary-key="{{ $this->realPrimaryKey }}"
                            :$radio
                            :$radioAttribute
                            :$checkbox
                            :$checkboxAttribute
                            :theme="$theme"
                            :$setUp
                            :$tableName
                            :parentName="$this->getName()"
                            :columns="$this->visibleColumns"
                            :data="$dataTransformer->transform($data->skip($skip)->take($take))->collection"
                        />
                    @endforeach
                </div>
            @endif

            @includeWhen($footerTotalColumn, 'livewire-powergrid::components.table-footer')
        @endif
    </x-slot:body>
</x-livewire-powergrid::table-base>
