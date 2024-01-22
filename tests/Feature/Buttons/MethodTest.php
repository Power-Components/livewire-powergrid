<?php

use Illuminate\Support\Facades\Route;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$route = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('route')
                ->slot('route: ' . $row->id)
                ->class('text-center')
                ->route('testing.route', ['dishId' => $row->id])
                ->method('post'),
        ];
    }
};

dataset('action:routeMethod', [
    'tailwind'       => [$route::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$route::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$route::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$route::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "routeMethod" on edit button', function (string $component, object $params) {
    Route::get('testing/{dishId}', function ($params) {
        return 'Testing route';
    })->name('testing.route');

    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtmlInOrder([
            '<form target="_self" action="http://localhost/testing/1" method="post">',
            '<input type="hidden" name="_method" value="post">',
            '<input type="hidden" name="_token" value="" autocomplete="off">',
            '<button type="submit" class="text-center">route: 1</button>',
            '</form>',
        ])
        ->assertSeeHtmlInOrder([
            '<form target="_self" action="http://localhost/testing/2" method="post">',
            '<input type="hidden" name="_method" value="post">',
            '<input type="hidden" name="_token" value="" autocomplete="off">',
            '<button type="submit" class="text-center">route: 2</button>',
            '</form>',
        ])
        ->call('setPage', 2)
        ->assertSeeHtmlInOrder([
            '<form target="_self" action="http://localhost/testing/7" method="post">',
            '<input type="hidden" name="_method" value="post">',
            '<input type="hidden" name="_token" value="" autocomplete="off">',
            '<button type="submit" class="text-center">route: 7</button>',
            '</form>',
        ]);
})
    ->with('action:routeMethod')
    ->group('action');
