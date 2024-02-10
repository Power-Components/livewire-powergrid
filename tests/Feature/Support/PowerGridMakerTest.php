<?php

use PowerComponents\LivewirePowerGrid\Enums\Datasource;
use PowerComponents\LivewirePowerGrid\Support\{PowerGridComponentMaker, PowerGridStub};

it('can make an eloquent component', function () {
    $component = PowerGridComponentMaker::make('UserTable')
      ->setDatasource(Datasource::ELOQUENT_BUILDER)
      ->loadPowerGridStub()
      ->setModelWithFqn('User', 'App\Models\User');

    expect($component)->toBeInstanceOf(PowerGridComponentMaker::class)
      ->name->toBe('UserTable')
      ->folder->toBeEmpty()
      ->datasource->toBe(Datasource::ELOQUENT_BUILDER)
      ->stub->toBeInstanceOf(PowerGridStub::class)
      ->model->toBe('User')
      ->modelFqn->toBe('App\Models\User')
      ->databaseTable->toBeEmpty()
      ->fqn->toBe('App\Livewire\UserTable')
      ->namespace->toBe('App\Livewire')
      ->filename->toBe('UserTable.php')
      ->htmlTag->toBe('<livewire:user-table/>')
      ->isProcessed->toBeFalse()
      ->autoCreateColumns->toBeFalse()
      ->usesCustomStub->toBeFalse();

    expect($component->createdPath())->toBe('app/Livewire/UserTable.php');

    expect($component->savePath())->toEndWith(str_replace('/', DIRECTORY_SEPARATOR, 'app/Livewire/UserTable.php'));
});

it('can make an query builder component', function () {
    $component = PowerGridComponentMaker::make('UserTable')
      ->setDatasource(Datasource::QUERY_BUILDER)
      ->setDatabaseTable('users')
      ->loadPowerGridStub();

    expect($component)->toBeInstanceOf(PowerGridComponentMaker::class)
      ->name->toBe('UserTable')
      ->folder->toBeEmpty()
      ->datasource->toBe(Datasource::QUERY_BUILDER)
      ->stub->toBeInstanceOf(PowerGridStub::class)
      ->model->toBeEmpty()
      ->modelFqn->toBeEmpty()
      ->databaseTable->toBe('users')
      ->fqn->toBe('App\Livewire\UserTable')
      ->namespace->toBe('App\Livewire')
      ->filename->toBe('UserTable.php')
      ->htmlTag->toBe('<livewire:user-table/>')
      ->isProcessed->toBeFalse()
      ->autoCreateColumns->toBeFalse()
      ->usesCustomStub->toBeFalse();

    expect($component->createdPath())->toBe('app/Livewire/UserTable.php');

    expect($component->savePath())->toEndWith(str_replace('/', DIRECTORY_SEPARATOR, 'app/Livewire/UserTable.php'));
});

it('acceptes different subfolder notations', function (string $name) {
    $component = PowerGridComponentMaker::make($name);

    expect($component)
      ->name->toBe('ListTable')
      ->namespace->toBe('App\Livewire\Users\Admins')
      ->folder->toBe('Users\Admins')
      ->fqn->toBe('App\Livewire\Users\Admins\ListTable')
      ->htmlTag->toBe('<livewire:users.admins.list-table/>');

    expect($component->createdPath())->toBe('app/Livewire/Users/Admins/ListTable.php');

    expect($component->savePath())->toEndWith(str_replace('/', DIRECTORY_SEPARATOR, 'app/Livewire/Users/Admins/ListTable.php'));
})
->with(
    ['Users.Admins.ListTable'],
    ['Users\Admins\ListTable'],
    ['Users/Admins/ListTable'],
    ['Users.Admins/ListTable']
);

it('can create component with 5 subfolder level', function () {
    $component = PowerGridComponentMaker::make('System/Office/Users/Admin/Active/ListTable');

    expect($component)
      ->name->toBe('ListTable')
      ->namespace->toBe('App\Livewire\System\Office\Users\Admin\Active')
      ->folder->toBe('System\Office\Users\Admin\Active')
      ->fqn->toBe('App\Livewire\System\Office\Users\Admin\Active\ListTable')
      ->htmlTag->toBe('<livewire:system.office.users.admin.active.list-table/>');

    expect($component->createdPath())->toBe("app/Livewire/System/Office/Users/Admin/Active/ListTable.php");

    expect($component->savePath())->toEndWith(str_replace('/', DIRECTORY_SEPARATOR, 'app/Livewire/System/Office/Users/Admin/Active/ListTable.php'));
});
