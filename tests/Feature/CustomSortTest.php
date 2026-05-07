<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('uses custom sort callback for collection datasource', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-custom-sort';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Apple Pie', 'calories' => 500],
                ['id' => 2, 'name' => 'Cherry Tart', 'calories' => 300],
                ['id' => 3, 'name' => 'Zebra Dish', 'calories' => 600],
                ['id' => 4, 'name' => 'Banana Split', 'calories' => 400],
                ['id' => 5, 'name' => 'Donut', 'calories' => 200],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('calories');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::make('Calories', 'calories')
                    ->sortUsing(function ($collection, $direction) {
                        return $collection->sortBy('calories', SORT_REGULAR, $direction === 'desc');
                    }),
            ];
        }
    };

    Livewire::test($component::class)
        ->call('sortBy', 'calories')
        ->set('sortDirection', 'asc')
        ->assertSeeInOrder(['Donut', 'Cherry Tart', 'Banana Split', 'Apple Pie', 'Zebra Dish'])
        ->set('sortDirection', 'desc')
        ->assertSeeInOrder(['Zebra Dish', 'Apple Pie', 'Banana Split', 'Cherry Tart', 'Donut']);
});

it('sortCallback is excluded from toLivewire serialization', function () {
    $column = Column::make('Test', 'test')
        ->sortUsing(fn ($c, $d) => $c);

    // Ensure sortCallback exists on the column object
    expect($column->sortCallback)->toBeInstanceOf(Closure::class);

    // But it should be excluded from the serialized array
    $serialized = $column->toLivewire();
    expect($serialized)->not->toHaveKey('sortCallback');
});
