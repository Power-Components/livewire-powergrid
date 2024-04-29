
<?php

use PowerComponents\LivewirePowerGrid\Actions\ListModels;

it('list all Eloquent Models in a directory', function () {
    app()->config->set('livewire-powergrid.auto_discover_models_paths', [
        'tests/Concerns/Models',
    ]);

    expect(ListModels::handle())->toBe(
        [
            'PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Category',
            'PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Chef',
            'PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish',
            'PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Order',
            'PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Restaurant',
        ]
    );
});

it('will not list non-Eloquent Models', function () {
    app()->config->set('livewire-powergrid.auto_discover_models_paths', [
        'tests/Concerns/Enums', //There are no models in this directory.

    ]);

    expect(ListModels::handle())->toBe([]);
});
