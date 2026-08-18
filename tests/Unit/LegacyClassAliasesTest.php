<?php

use PowerComponents\LivewirePowerGrid\{Button, Column, Fields};
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterInputText;
use PowerComponents\LivewirePowerGrid\Components\Rules\RuleActions;
use PowerComponents\LivewirePowerGrid\Components\SetUp\{Cache, Detail, Exportable, FilterBuilder, Footer, Header, Responsive};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid, Rule};

it('resolves legacy LivewirePowerGrid core classes transparently via class aliases', function (string $legacyClass) {
    expect(class_exists($legacyClass))->toBeTrue();
})->with([
    Button::class,
    Column::class,
    Fields::class,
    Exportable::class,
    Header::class,
    Footer::class,
    Detail::class,
    Cache::class,
    Responsive::class,
    FilterBuilder::class,
    FilterInputText::class,
    RuleActions::class,
    Filter::class,
    PowerGrid::class,
    Rule::class,
]);

it('allows instantiating Button via legacy namespace', function () {
    $button = Button::make('edit')->slot('Edit');

    expect($button)->toBeInstanceOf(PowerComponents\Turbine\Button::class)
        ->and($button->action)->toBe('edit')
        ->and($button->slot)->toBe('Edit');
});

it('allows instantiating Column via legacy namespace', function () {
    $column = Column::make('Name', 'name');

    expect($column)->toBeInstanceOf(PowerComponents\Turbine\Column::class)
        ->and($column->title)->toBe('Name')
        ->and($column->field)->toBe('name');
});

it('allows instantiating Fields via legacy namespace', function () {
    $fields = new Fields();

    expect($fields)->toBeInstanceOf(PowerComponents\Turbine\Fields::class);
});

it('allows instantiating Exportable via legacy namespace', function () {
    $exportable = new Exportable('dishes');

    expect($exportable)->toBeInstanceOf(PowerComponents\Turbine\Components\SetUp\Exportable::class);
});
