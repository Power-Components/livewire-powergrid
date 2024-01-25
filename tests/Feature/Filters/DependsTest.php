<?php

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Category;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\{Chef};

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function filters(): array
    {
        return [
            Filter::select('category_name', 'category_id')
                ->dataSource(Category::all())
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('chef_name', 'chef_id')
                ->depends(['category_id'])
                ->dataSource(
                    fn ($depends) => Chef::query()
                    ->when(
                        isset($depends['category_id']),
                        fn (Builder $query) => $query->whereRelation(
                            'categories',
                            fn (Builder $builder) => $builder->where('id', $depends['category_id'])
                        )
                    )
                    ->get()
                )
                ->optionLabel('name')
                ->optionValue('id'),
        ];
    }
};

dataset('action:depends', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('"depends" works properly in select', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtmlInOrder([
            'Luan',
            'Dan',
            'Vitor',
            'Claudio',
        ])
        ->set('filters.select.category_id', 1)
        ->assertSeeHtmlInOrder([
            'Luan',
            'Claudio',
        ])
        ->assertDontSeeHtml([
            'Dan',
            'Vitor',
        ]);
})
    ->with('action:depends')
    ->group('action')
    ->skip('');
