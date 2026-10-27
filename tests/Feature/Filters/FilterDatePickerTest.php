<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

it('properly filters by datepicker', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-datepicker-filter';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'produced_at' => '2021-01-01'],
                ['id' => 2, 'name' => 'Dish 2', 'produced_at' => '2021-02-02'],
                ['id' => 3, 'name' => 'Dish 3', 'produced_at' => '2021-03-03'],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::datepicker('produced_at'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->assertSee('Dish 1')
        ->assertSee('Dish 2')
        ->assertSee('Dish 3')
        ->dispatch('pg:datePicker-test-datepicker-filter',
            field: 'produced_at',
            selectedDates: ['2021-01-01', '2021-02-02'],
            dateStr: '2021-01-01 to 2021-02-02',
            label: 'Produced At',
            type: 'date',
            timezone: 'UTC',
            dateFormat: 'Y-m-d'
        )
        ->assertSee('Dish 1')
        ->assertSee('Dish 2')
        ->assertDontSee('Dish 3')
        ->call('clearFilter', 'produced_at')
        ->assertSee('Dish 3');
});

it('reads the picked range in the filter timezone param', function () {
    config()->set('app.timezone', 'UTC');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-datepicker-timezone';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'produced_at' => '2021-01-01 02:00:00'],
                ['id' => 2, 'name' => 'Dish 2', 'produced_at' => '2021-01-01 05:00:00'],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::datetimepicker('produced_at')->params(['timezone' => 'America/Sao_Paulo']),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->dispatch('pg:datePicker-test-datepicker-timezone',
            field: 'produced_at',
            selectedDates: ['2021-01-01 00:00', '2021-01-01 06:00'],
            dateStr: '2021-01-01 00:00 to 2021-01-01 06:00',
            label: 'Produced At',
            type: 'datetime',
            timezone: 'America/Sao_Paulo',
            dateFormat: 'Y-m-d H:i'
        )
        ->assertDontSee('Dish 1')
        ->assertSee('Dish 2');
});

it('reads the timezone param when the filter field is table-qualified', function () {
    config()->set('app.timezone', 'UTC');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-datepicker-timezone-qualified';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1', 'produced_at' => '2021-01-01 05:00:00']]);
        }

        public function filters(): array
        {
            return [
                Filter::datetimepicker('produced_at_formatted', 'dishes.produced_at')->params(['timezone' => 'America/Sao_Paulo']),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    $range = Livewire::test($component::class)
        ->set('draftFilters.produced_at_formatted.value.formatted', '2021-01-01 00:00 to 2021-01-01 06:00')
        ->call('applyFilters')
        ->get('filters.produced_at_formatted.value');

    expect($range['start'])->toBe('Fri Jan 01 2021 03:00:00 GMT+0000');
});
