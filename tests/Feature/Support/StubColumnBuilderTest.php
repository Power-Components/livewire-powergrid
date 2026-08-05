<?php

use PowerComponents\LivewirePowerGrid\Commands\Support\StubColumnBuilder;

it('builds columns, filters and fields for every column type', function () {
    $result = (new StubColumnBuilder())->build([
        'id' => 'bigint',
        'name' => 'string',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'born_at' => 'date',
    ], 'User');

    expect($result['columns'])
        ->toContain("Column::make('Id', 'id'),")
        ->toContain("Column::make('Name', 'name')")
        ->toContain('->sortable()')
        ->toContain('->searchable()')
        ->toContain("Column::make('Active', 'active')")
        ->toContain('->toggleable()')
        ->toContain("Column::make('Created at', 'created_at_formatted', 'created_at')")
        ->toContain("Column::make('Born at', 'born_at_formatted', 'born_at')")
        ->and($result['filters'])
        ->toContain("Filter::inputText('name')->operators(['contains'])")
        ->toContain("Filter::boolean('active')")
        ->toContain("Filter::datetimepicker('created_at')")
        ->toContain("Filter::datepicker('born_at')")
        ->and($result['PowerGridFields'])
        ->toContain("->add('id')")
        ->toContain("->add('name')")
        ->toContain("->add('active')")
        ->toContain("->add('created_at_formatted', fn (User \$model) => Carbon::parse(\$model->created_at)->format('d/m/Y H:i:s'))")
        ->toContain("->add('born_at_formatted', fn (User \$model) => Carbon::parse(\$model->born_at)->format('d/m/Y'))");
});

it('applies the model type-hint to generated closures', function () {
    $result = (new StubColumnBuilder())->build(['name' => 'string'], 'Dish');

    expect($result['PowerGridFields'])
        ->toContain('fn (Dish $model) => strtolower(e($model->name))');
});

it('omits the type-hint when none is given', function () {
    $result = (new StubColumnBuilder())->build(['name' => 'string']);

    expect($result['PowerGridFields'])
        ->toContain('fn ($model) => strtolower(e($model->name))')
        ->not->toContain('fn ( $model)');
});

it('adds the escape example only once', function () {
    $result = (new StubColumnBuilder())->build([
        'name' => 'string',
        'email' => 'string',
    ], 'User');

    expect(substr_count($result['PowerGridFields'], 'Example of custom column using a closure'))
        ->toBe(1);
});

it('appends the action column by default', function () {
    $result = (new StubColumnBuilder())->build(['id' => 'bigint']);

    expect($result['columns'])->toContain("Column::action('Action')");
});

it('can skip the action column', function () {
    $result = (new StubColumnBuilder())->build(['id' => 'bigint'], '', false);

    expect($result['columns'])->not->toContain("Column::action('Action')");
});

it('closes the columns and filters arrays', function () {
    $result = (new StubColumnBuilder())->build(['id' => 'bigint']);

    expect($result['columns'])->toEndWith('];')
        ->and($result['filters'])->toStartWith('[')->toEndWith('];');
});
