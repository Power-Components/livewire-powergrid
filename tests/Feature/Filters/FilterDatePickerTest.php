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
