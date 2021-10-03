<?php

use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\DishesTable;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->columns         = [
        Column::add()
            ->title('Id')
            ->field('id')
            ->searchable()
            ->sortable(),

        Column::add()
            ->title('Name')
            ->field('name')
            ->searchable()
            ->editOnClick(true)
            ->clickToCopy(true)
            ->makeInputText('name')
            ->sortable(),
        ];

    $component = new PowerGridComponent(1);
    $component->datasource = Dish::query();
    $component->columns    = $this->columns;
    $component->perPage    = 10;

    $this->component       = $component;
});

it('properly filters by "name is"', function () {
    $component = livewire(DishesTable::class);
    $component->set('filters', filterInputText('Peixada da chef Nábia', 'is'));
    $component->assertSeeHtml('Peixada da chef Nábia');
});

it('properly filters by "name is" when name is not present', function () {
    $component = livewire(DishesTable::class);
    $component->set('filters', filterInputText('Peixada do João', 'is'));
    $component->assertDontSeeText('Peixada do João');
});

it('properly filters by "name is not"', function () {
    $this->component->filters = filterInputText('Peixada da chef Nábia', 'is_not');
    $pagination               = $this->component->fillData();

    expect($pagination->total())->toBe(101);
});

it('properly filters by "name is not" when name is not present', function () {
    $this->component->filters = filterInputText('Peixada do João', 'is_not');
    $pagination               = $this->component->fillData();

    expect($pagination->total())->toBe(102);
});

it('properly filters by "name contains"', function () {
    $this->component->filters = filterInputText('Tomates', 'contains');
    $pagination               = $this->component->fillData();

    expect($pagination->items())
        ->toHaveCount(1);

    expect($pagination->first()->name)
        ->toContain('Sopa de Tomates Assados');
});


it('properly filters by "name contains" when name is not present', function () {
    $this->component->filters = filterInputText('Luan', 'contains');
    $pagination               = $this->component->fillData();

    expect($pagination->total())->toBe(0);
});

it('properly filters by "name starts_with"', function () {
    $this->component->filters = filterInputText('Sopa', 'starts_with');
    $pagination               = $this->component->fillData();

    expect($pagination->items())->toHaveCount(2);

    expect($pagination->first()->name)
        ->toContain('Tomates Assados');
});

it('properly filters by "name starts_with" when name is not present', function () {
    $this->component->filters = filterInputText('mamão', 'starts_with');
    $pagination               = $this->component->fillData();

    expect($pagination->items())->toHaveCount(0);
});

it('properly filters by "name ends_with"', function () {
    $this->component->filters = filterInputText('Assados', 'ends_with');
    $pagination               = $this->component->fillData();

    expect($pagination->items())
        ->toHaveCount(1);

    expect($pagination->first()->name)
        ->toContain('Sopa de Tomates');
});


it('properly filters by "name ends_with" when name is not present', function () {
    $this->component->filters = filterInputText('Panqueca', 'ends_with');
    $pagination               = $this->component->fillData();

    expect($pagination->items())->toHaveCount(0);
});

