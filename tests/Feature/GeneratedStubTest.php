<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

$renderStub = function (string $stub, string $componentName): string {
    $code = str_replace(
        ['{{ namespace }}', '{{ componentName }}', '{{ tableName }}', '{{ databaseTableName }}', '{{ modelFqn }}', '{{ model }}'],
        ['PowerComponents\LivewirePowerGrid\Tests\Generated', $componentName, strtolower($componentName), 'dishes', Dish::class, 'Dish'],
        file_get_contents(__DIR__.'/../../resources/stubs/'.$stub),
    );

    eval(substr($code, strlen('<?php')));

    return 'PowerComponents\LivewirePowerGrid\Tests\Generated\\'.$componentName;
};

it('renders the component generated from the query builder stub', function () use ($renderStub) {
    $dish = Dish::query()->first();

    Livewire::test($renderStub('table.query-builder.stub', 'GeneratedQueryBuilderTable'))
        ->assertOk()
        ->assertSee($dish->name);
});
