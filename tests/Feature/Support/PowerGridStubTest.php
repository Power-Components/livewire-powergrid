<?php

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use PowerComponents\LivewirePowerGrid\Support\PowerGridStub;

it('can make and render a stub', function () {
    $stub = PowerGridStub::make(powergrid_stubs_path('table.model.stub'))
      ->setVar('modelLastName', 'Banana')
      ->render();

    expect($stub)->toContain('return Banana::');
});

it('can set and unset stub vars', function () {
    $stub = PowerGridStub::make(powergrid_stubs_path('table.model.stub'))
      ->setVar('componentName', 'UserTable')
      ->setVar('componentVersion', '1x')
      ->setVar('modelLastName', 'User')
      ->unsetVar('componentVersion');

    expect($stub->listVars()->toArray())->toBe([
        'componentName' => 'UserTable',
        'modelLastName' => 'User',
    ]);
});

it('throws FileNotFoundException if template does not exist', function (string $invalidTemplate) {
    PowerGridStub::make($invalidTemplate);
})
->with(['', 'DOES-NOT-EXIST'])
->throws(FileNotFoundException::class);
