<?php

use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;


it('properly paginates data', function () {
    $component = new PowerGridComponent(1);
    $component->datasource = Dish::query();
    $component->perPage    = 10;

    $pagination = $component->fillData();

    expect($pagination->total())->toBe(102);
    expect($pagination->perPage())->toBe(10);
});



