<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\DataSource\Builders\Select;

uses()->group('filters', 'security', 'database');

function nestedKeyComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'nested-key';
    };
}

it('appends a plain identifier key onto the column', function () {
    $query = Dish::query();

    (new Select(nestedKeyComponent()))->builder($query, 'name', ['en' => 'Pastel']);

    expect($query->toSql())->toContain('en')
        ->and($query->getBindings())->toContain('Pastel');
});

it('ignores an unexpected key and keeps the base column', function () {
    $query = Dish::query();

    (new Select(nestedKeyComponent()))->builder($query, 'name', ['bad key' => 'Pastel']);

    expect($query->toSql())->not->toContain('bad')
        ->and($query->getBindings())->toContain('Pastel');
});

it('does not append a client inputText value key onto the sql column', function () {
    Dish::query()->forceDelete();
    Dish::create([
        'name' => 'Alpha',
        'category_id' => 1,
        'chef_id' => 1,
        'diet' => 1,
        'price' => 10,
        'calories' => 100,
        'stored_at' => 'needle-value',
        'produced_at' => now(),
    ]);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'nested-key-input';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::inputText('name')];
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
        ->set('filters', [
            'name' => [
                'type' => 'input_text',
                'op' => 'is',
                'value' => ['stored_at' => 'needle-value'],
            ],
        ])
        ->assertSee('Alpha');
});
