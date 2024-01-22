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
                ->route('testing.route', ['dishId' => $row->id]),
        ];
    }
};

dataset('action:route', [
    'tailwind'       => [$route::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$route::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$route::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$route::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

$routeTarget = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('route')
                ->slot('route: ' . $row->id)
                ->route('testing.route', ['dishId' => $row->id])
                ->target('_blank'),
        ];
    }
};

dataset('action:routeTarget', [
    'tailwind'       => [$routeTarget::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$routeTarget::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$routeTarget::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$routeTarget::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "route" on edit button', function (string $component, object $params) {
    Route::get('testing/{dishId}', function ($params) {
        return 'Testing route';
    })->name('testing.route');

    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("href=\"http://localhost/testing/1\">route: 1</a>")
        ->assertSeeHtml("href=\"http://localhost/testing/2\">route: 2</a>")
        ->assertSeeHtml("href=\"http://localhost/testing/3\">route: 3</a>")
        ->call('setPage', 2)
        ->assertSeeHtml("href=\"http://localhost/testing/7\">route: 7</a>")
        ->assertDontSeeHtml("href=\"http://localhost/testing/1\">route: 1</a>");
})
    ->with('action:route')
    ->group('action');

it('properly displays "routeTarget" on edit button', function (string $component, object $params) {
    Route::get('testing/{dishId}', function ($params) {
        return 'Testing route target';
    })->name('testing.route');

    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("target=\"_blank\" href=\"http://localhost/testing/1\">route: 1</a>")
        ->assertSeeHtml("target=\"_blank\" href=\"http://localhost/testing/2\">route: 2</a>")
        ->assertSeeHtml("target=\"_blank\" href=\"http://localhost/testing/3\">route: 3</a>")
        ->call('setPage', 2)
        ->assertSeeHtml("target=\"_blank\" href=\"http://localhost/testing/7\">route: 7</a>")
        ->assertDontSeeHtml("target=\"_blank\" href=\"http://localhost/testing/1\">route: 1</a>");
})
    ->with('action:routeTarget')
    ->group('action');
