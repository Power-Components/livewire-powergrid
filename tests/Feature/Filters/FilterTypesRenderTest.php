<?php

use Illuminate\Support\Collection;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Enums\Diet;
use PowerComponents\Turbine\Components\Filters\{FilterEnumSelect, FilterMultiSelectAsync};

uses()->group('filters');

it('builds an enum select filter and resolves its option labels', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'enum-select';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Vegan Dish', 'diet' => 1],
                ['id' => 2, 'name' => 'Celiac Dish', 'diet' => 2],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::enumSelect('diet')
                    ->dataSource(Diet::cases()),
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

    // Rendering resolves the filters, invoking FilterEnumSelect::execute(),
    // which maps each enum case using labelTurbineFilter().
    Livewire::test($component::class)
        ->assertOk()
        ->assertSee('Vegan Dish')
        ->assertSee('Celiac Dish');
});

it('resolves enum option labels through execute() using labelTurbineFilter', function () {
    $filter = (new FilterEnumSelect('diet'))->dataSource(Diet::cases());

    $filter->execute();

    /** @var Collection<int, array<string, mixed>> $source */
    $source = collect($filter->dataSource);

    expect($filter->optionLabel)->toBe('name')
        ->and($source->pluck('name'))->toContain('🌱 Suitable for Vegans');
});

it('builds a multi select async filter with its fluent setters', function () {
    $filter = (new FilterMultiSelectAsync('category_id'))
        ->url('https://example.test/options')
        ->method('post')
        ->parameters(['scope' => 'active'])
        ->optionValue('id')
        ->optionLabel('name');

    expect($filter->key)->toBe('multi_select')
        ->and($filter->url)->toBe('https://example.test/options')
        ->and($filter->method)->toBe('post')
        ->and($filter->parameters)->toBe(['scope' => 'active'])
        ->and($filter->optionValue)->toBe('id')
        ->and($filter->optionLabel)->toBe('name');
});

it('renders a component that declares a multi select async filter without errors', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'multi-select-async';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1', 'category_id' => 1]]);
        }

        public function filters(): array
        {
            return [
                Filter::multiSelectAsync('category_id')
                    ->url('https://example.test/categories')
                    ->optionValue('category_id')
                    ->optionLabel('name'),
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
        ->assertOk()
        ->assertSee('Dish 1');
});
