<?php

use PowerComponents\LivewirePowerGrid\PowerGridComponent;

it('properly paginates data', function () {
    $component = new PowerGridComponent(1);
    $component->datasource = testDatasource();
    $component->perPage    = 2;

    $pagination            = $component->fillData();

    expect($pagination->total())->toBe(4);
    expect($pagination->perPage())->toBe(2);
});


