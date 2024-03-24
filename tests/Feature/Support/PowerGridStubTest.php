<?php

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use PowerComponents\LivewirePowerGrid\Support\PowerGridStub;

it('can make and render a stub', function () {
    $stub = PowerGridStub::make(powergrid_stubs_path('table.model.stub'))
      ->setVar('model', 'Banana')
      ->render();

    expect($stub)->toContain('return Banana::');
});

it('can set and unset stub vars', function () {
    $stub = PowerGridStub::make(powergrid_stubs_path('table.model.stub'))
      ->setVar('componentName', 'UserTable')
      ->setVar('componentVersion', '1x')
      ->setVar('model', 'User')
      ->unsetVar('componentVersion');

    expect($stub->listVars()->toArray())->toBe([
        'componentName' => 'UserTable',
        'model'         => 'User',
    ]);
});

it('can list all variables in stub file', function () {
    expect(PowerGridStub::make(fixturePath('Stubs/demo.stub')))
      ->listStubVars()
      ->toArray()
      ->toBe([
          'Var1',
          'fooBar',
          'FooBarBaz2',
          'SomeVar',
      ]);
});

it('properly replaces legacy variables', function () {
    $stub = PowerGridStub::make(fixturePath('Stubs/legacy.stub'))
      ->setVar('namespace', 'App\Livewire')
      ->setVar('model', 'User')
      ->setVar('modelFqn', 'App\Models\User')
      ->setVar('databaseTableName', 'users')
      ->setVar('PowerGridFields', "->add('created_at')")
      ->render();

    expect($stub)
    ->toContain('namespace App\Livewire;')
    ->toContain('use App\Models\User;')
    ->toContain('public function actions(User $row): array')
    ->toContain("return PowerGrid::fields()->add('created_at');")
    ->toContain("return DB::table('users');");
});

it('throws FileNotFoundException if template does not exist', function (string $invalidTemplate) {
    PowerGridStub::make($invalidTemplate);
})
->with(['', 'DOES-NOT-EXIST'])
->throws(FileNotFoundException::class);
