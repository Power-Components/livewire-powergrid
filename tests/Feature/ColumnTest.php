<?php

use PowerComponents\LivewirePowerGrid\Column;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{Concerns\Components\DishesQueryBuilderTable,
    Concerns\Components\DishesTable,
    Concerns\Components\DishesTableWithJoin};

it('sorts by "name" and then by "id"', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->call('sortBy', $params->field)
        ->assertSeeHtml('Almôndegas ao Sugo')
        ->assertDontSeeHtml('Pastel de Nata')
        ->call('sortBy', 'id')
        ->assertSeeHtml('Pastel de Nata');
})->with('column_join', 'column_query_builder');

it('searches data', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->set('search', 'Sugo')
        ->assertDontSee('Pastel de Nata');
})->with('column_join', 'column_query_builder');

$contentClassesString = new class () extends DishesTable {
    public function columns(): array
    {
        return [
            Column::add()
                ->title('Id')
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Dish')
                ->field('name')
                ->searchable()
                ->contentClasses('bg-custom-500 text-custom-500')
                ->sortable(),

            Column::add()
                ->title(__('Stored at'))
                ->field('storage_room')
                ->sortable(),

            Column::action('Action'),
        ];
    }
};

it('add contentClasses on dishes name column', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSee('Pastel de Nata')
        ->assertSeeHtmlInOrder([
            '<span class=" ">',
            '<div>1</div>',
            '</span>',
            '<span class=" bg-custom-500 text-custom-500">',
            '<div>Pastel de Nata</div>',
            '</span>',
        ]);
})->with([
    'tailwind'  => [$contentClassesString::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class, 'field' => 'name']],
    'bootstrap' => [$contentClassesString::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class, 'field' => 'name']],
]);

$contentClassesArray = new class () extends DishesTable {
    public function columns(): array
    {
        return [
            Column::add()
                ->title('Id')
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Dish')
                ->field('name')
                ->searchable()
                ->contentClasses(['Peixada da chef Nábia' => 'bg-custom-500 text-custom-500'])
                ->sortable(),

            Column::add()
                ->title(__('Stored at'))
                ->field('storage_room')
                ->sortable(),

            Column::action('Action'),
        ];
    }
};

it('add contentClasses on dishes name column array', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSee('Pastel de Nata')
        ->assertSeeHtmlInOrder([
            '<span class=" ">',
            '<div>1</div>',
            '</span>',
            '<span class=" ">',
            '<div>Pastel de Nata</div>',
            '</span>',
            '<span class=" bg-custom-500 text-custom-500">',
            '<div>Peixada da chef Nábia</div>',
            '</span>',
        ]);
})->with([
    'tailwind'  => [$contentClassesArray::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class, 'field' => 'name']],
    'bootstrap' => [$contentClassesArray::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class, 'field' => 'name']],
]);

dataset('column_join', [
    'tailwind'       => [DishesTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class, 'field' => 'name']],
    'bootstrap'      => [DishesTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class, 'field' => 'name']],
    'tailwind join'  => [DishesTableWithJoin::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class, 'field' => 'dishes.name']],
    'bootstrap join' => [DishesTableWithJoin::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class, 'field' => 'dishes.name']],
]);

dataset('column_query_builder', [
    'tailwind query builder -> id'  => [DishesQueryBuilderTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class, 'field' => 'id']],
    'bootstrap query builder -> id' => [DishesQueryBuilderTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class, 'field' => 'id']],
]);
