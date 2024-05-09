<?php

use PowerComponents\LivewirePowerGrid\Facades\Filter;

use PowerComponents\LivewirePowerGrid\Tests\{Concerns\Components\DishesQueryBuilderTable,
    Concerns\Components\DishesTable,
    Concerns\Components\DishesTableWithJoin};

$component = new class () extends DishesTable {
    public function filters(): array
    {
        return [
            Filter::number('price_BRL')->placeholder('min_xyz_placeholder', 'max_xyz_placeholder')->thousands("'")->decimal(','),
            Filter::number('price') ->placeholder('min_xyz_placeholder', 'max_xyz_placeholder')->thousands("'")->decimal(','),
            Filter::inputText('name')->placeholder('dish_name_xyz_placeholder')->operators(),
            Filter::number('price')->placeholder('min_xyz_placeholder', 'max_xyz_placeholder')->thousands("'")->decimal(','),
            Filter::boolean('in_stock'),
        ];
    }
};

$componentQueryBuilder = new class () extends DishesQueryBuilderTable {
    public function filters(): array
    {
        return [
            Filter::number('price_BRL')->placeholder('min_xyz_placeholder', 'max_xyz_placeholder')->thousands("'")->decimal(','),
            Filter::number('price') ->placeholder('min_xyz_placeholder', 'max_xyz_placeholder')->thousands("'")->decimal(','),
            Filter::inputText('name')->placeholder('dish_name_xyz_placeholder')->operators(),
            Filter::number('price')->placeholder('min_xyz_placeholder', 'max_xyz_placeholder')->thousands("'")->decimal(','),
            Filter::boolean('in_stock'),
        ];
    }
};

$componentJoin = new class () extends DishesTableWithJoin {
    public function filters(): array
    {
        return [
            Filter::number('price_BRL') ->placeholder('min_xyz_placeholder', 'max_xyz_placeholder')->thousands("'")->decimal(','),
            Filter::inputText('dish_name')->placeholder('dish_name_xyz_placeholder')->operators(),
            Filter::number('price')->placeholder('min_xyz_placeholder', 'max_xyz_placeholder')->thousands("'")->decimal(','),
            Filter::boolean('in_stock'),
        ];
    }
};
