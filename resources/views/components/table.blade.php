<x-livewire-powergrid::table-base
    :$readyToLoad
    :$tableName
    :$theme
    :lazy="!is_null(data_get($setUp, 'lazy'))"
>
    <x-slot:header>
        @include('livewire-powergrid::components.table.tr')
    </x-slot:header>

    <x-slot:loading>
        @include('livewire-powergrid::components.table.tr', ['loading' => true])
    </x-slot:loading>

    <x-slot:body>
        @includeWhen($this->hasColumnFilters, 'livewire-powergrid::components.inline-filters')

        @if (is_null($data) || count($data) === 0)
            @include('livewire-powergrid::components.table.th-empty')
        @else
            @includeWhen($headerTotalColumn, 'livewire-powergrid::components.table-header')

            @if (empty(data_get($setUp, 'lazy')))

                @if (isset($setUp['detail']))
                    @foreach ($data as $row)
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
                            @if (data_get($setUp, 'detail.state.' . $rowId))
                                <tr
                                    class="{{ $class }}"
                                >
                                    @include('livewire-powergrid::components.table.detail')
                                </tr>
                            @endif
                        </tbody>

                        @includeWhen(isset($setUp['responsive']),
                            'livewire-powergrid::components.expand-container')
                    @endforeach
                @else
                    @foreach ($data as $row)
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
                    @foreach (range(0, data_get($setUp, 'lazy.items')) as $item)
                        @php
                            $skip = $item * data_get($setUp, 'lazy.rowsPerChildren');
                            $take = data_get($setUp, 'lazy.rowsPerChildren');
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
                            :data="\PowerComponents\LivewirePowerGrid\DataSource\Processors\DataSourceBase::transform(
                                $data->skip($skip)->take($take),
                                $this,
                                true,
                            )"
                        />
                    @endforeach
                </div>
            @endif

            @includeWhen($footerTotalColumn, 'livewire-powergrid::components.table-footer')
        @endif

    </x-slot:body>
</x-livewire-powergrid::table-base>
