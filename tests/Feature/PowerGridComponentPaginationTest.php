<?php

use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\ModelStub;


it('properly paginates data', function () {
    $component = new PowerGridComponent(1);
    $component->datasource = ModelStub::query();
    $component->perPage    = 10;

    $pagination = $component->fillData();

    expect($pagination->total())->toBe(306);
    expect($pagination->perPage())->toBe(10);
});



