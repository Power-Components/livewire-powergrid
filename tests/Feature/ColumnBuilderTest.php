<?php

use PowerComponents\LivewirePowerGrid\Column;

uses()->group('column');

it('sets every fluent column attribute', function () {
    $column = Column::make('Name', 'name', 'dishes.name')
        ->fixedOnResponsive()
        ->placeholder('Type the name')
        ->headerAttribute('text-left', 'width:10px')
        ->bodyAttribute('text-right', 'color:red')
        ->hidden(true, false)
        ->contentClassField('name_class')
        ->contentClasses(['a' => 'x'])
        ->template()
        ->visibleInExport(false);

    expect($column->fixedOnResponsive)->toBeTrue()
        ->and($column->placeholder)->toBe('Type the name')
        ->and($column->headerClass)->toBe('text-left')
        ->and($column->headerStyle)->toBe('width:10px')
        ->and($column->bodyClass)->toBe('text-right')
        ->and($column->bodyStyle)->toBe('color:red')
        ->and($column->hidden)->toBeTrue()
        ->and($column->forceHidden)->toBeFalse()
        ->and($column->contentClassField)->toBe('name_class')
        ->and($column->contentClasses)->toBe(['a' => 'x'])
        ->and($column->template)->toBeTrue()
        ->and($column->visibleInExport)->toBeFalse();
});

it('marks the column as index and searchable', function () {
    $column = Column::add()->title('Id')->field('id')->index()->searchable();

    expect($column->index)->toBeTrue()
        ->and($column->searchable)->toBeTrue()
        ->and($column->field)->toBe('id');
});

it('registers a custom sort callback with sortUsing', function () {
    $callback = fn ($query, $direction) => $query;

    $column = Column::make('Price', 'price')->sortUsing($callback);

    expect($column->sortable)->toBeTrue()
        ->and($column->enableSort)->toBeTrue()
        ->and($column->sortCallback)->toBe($callback);
});
