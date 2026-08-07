<?php

use PowerComponents\LivewirePowerGrid\Commands\Actions\BuildStubVars;

it('generates a searchable, sortable column with a text filter for a string', function () {
    $vars = BuildStubVars::handle(['name'], collect(['name' => 'string']));

    expect($vars['columns'])->toContain("Column::make('Name', 'name')\n                ->sortable()\n                ->searchable(),")
        ->and($vars['PowerGridFields'])->toContain("->add('name')")
        ->and($vars['filters'])->toContain("Filter::inputText('name')->operators(['contains']),");
});

it('generates a plain column without a filter for an integer', function () {
    $vars = BuildStubVars::handle(['votes'], collect(['votes' => 'integer']));

    expect($vars['columns'])->toContain("Column::make('Votes', 'votes'),")
        ->and($vars['PowerGridFields'])->toContain("->add('votes')")
        ->and($vars['filters'])->not->toContain('votes');
});

it('generates a toggleable column with a boolean filter for a boolean', function () {
    $vars = BuildStubVars::handle(['in_stock'], collect(['in_stock' => 'boolean']));

    expect($vars['columns'])->toContain("Column::make('In stock', 'in_stock')\n                ->toggleable(),")
        ->and($vars['PowerGridFields'])->toContain("->add('in_stock')")
        ->and($vars['filters'])->toContain("Filter::boolean('in_stock'),");
});

it('generates a formatted column with a date filter for a date', function () {
    $vars = BuildStubVars::handle(['published_on'], collect(['published_on' => 'date']));

    expect($vars['columns'])->toContain("Column::make('Published on', 'published_on_formatted', 'published_on')")
        ->and($vars['PowerGridFields'])->toContain("->add('published_on_formatted', fn (\$model) => Carbon::parse(\$model->published_on)->format('d/m/Y'))")
        ->and($vars['filters'])->toContain("Filter::datepicker('published_on'),");
});

it('generates a formatted column with a datetime filter for a datetime', function () {
    $vars = BuildStubVars::handle(['created_at'], collect(['created_at' => 'datetime']));

    expect($vars['columns'])->toContain("Column::make('Created at', 'created_at_formatted', 'created_at')")
        ->and($vars['PowerGridFields'])->toContain("->add('created_at_formatted', fn (\$model) => Carbon::parse(\$model->created_at)->format('d/m/Y H:i:s'))")
        ->and($vars['filters'])->toContain("Filter::datetimepicker('created_at'),");
});

it('falls back to a searchable column without a filter for an unknown type', function () {
    $vars = BuildStubVars::handle(['payload'], collect(['payload' => 'other']));

    expect($vars['columns'])->toContain("Column::make('Payload', 'payload')\n                ->sortable()\n                ->searchable(),")
        ->and($vars['PowerGridFields'])->toContain("->add('payload')")
        ->and($vars['filters'])->toBe("[\n        ];");
});

it('adds the closure example to the first string field only', function () {
    $vars = BuildStubVars::handle(['name', 'email'], collect(['name' => 'string', 'email' => 'string']));

    expect(substr_count($vars['PowerGridFields'], 'Example of custom column using a closure'))->toBe(1)
        ->and($vars['PowerGridFields'])->toContain("->add('name_lower', fn (\$model) => strtolower(e(\$model->name)))")
        ->and($vars['PowerGridFields'])->not->toContain('email_lower');
});

it('type-hints the model in generated closures', function () {
    $vars = BuildStubVars::handle(['name', 'created_at'], collect(['name' => 'string', 'created_at' => 'datetime']), 'Dish');

    expect($vars['PowerGridFields'])
        ->toContain('fn (Dish $model) => strtolower(e($model->name))')
        ->toContain('fn (Dish $model) => Carbon::parse($model->created_at)');
});

it('skips fields that have no known column type', function () {
    $vars = BuildStubVars::handle(['name', 'ghost'], collect(['name' => 'string']));

    expect($vars['PowerGridFields'])->not->toContain('ghost')
        ->and($vars['columns'])->not->toContain('ghost');
});

it('keeps the given field order', function () {
    $vars = BuildStubVars::handle(['votes', 'id'], collect(['id' => 'integer', 'votes' => 'integer']));

    expect($vars['columns'])->toContain("Column::make('Votes', 'votes'),\n            Column::make('Id', 'id'),");
});

it('always closes the component with an action column', function () {
    $vars = BuildStubVars::handle([], collect());

    expect($vars['columns'])->toBe("[\n            Column::action('Action')\n        ];")
        ->and($vars['filters'])->toBe("[\n        ];")
        ->and($vars['PowerGridFields'])->toBe('');
});
