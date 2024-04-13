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

    expect($component->savePath($component->filename))->toEndWith(str_replace('/', DIRECTORY_SEPARATOR, 'app/Livewire/UserTable.php'));
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

    expect($component->savePath($component->filename))->toEndWith(str_replace('/', DIRECTORY_SEPARATOR, 'app/Livewire/UserTable.php'));
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

    expect($component->savePath($component->filename))->toEndWith(str_replace('/', DIRECTORY_SEPARATOR, 'app/Livewire/Users/Admins/ListTable.php'));
})
->with(
    ['Users.Admins.ListTable'],
    ['Users\Admins\ListTable'],
    ['Users/Admins/ListTable'],
    ['Users.Admins/ListTable'],
    ['Users.Admins/ListTable'],
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

    expect($component->savePath($component->filename))->toEndWith(str_replace('/', DIRECTORY_SEPARATOR, 'app/Livewire/System/Office/Users/Admin/Active/ListTable.php'));
});

it('creates proper components with different name text cases ', function (array $expected) {
    expect(PowerGridComponentMaker::make($expected['name']))
      ->namespace->toBe($expected['namespace'])
      ->fqn->toBe($expected['fqn'])
      ->htmlTag->toBe($expected['html_tag'])
    ->createdPath()->toBe($expected['created_path'])
     ->not->toBeNull();
})
->with(
    [
        'SysAdmins' => [[
            'name'         => 'Users\SysAdmins\UserIndexTable',
            'fqn'          => 'App\Livewire\Users\SysAdmins\UserIndexTable',
            'namespace'    => 'App\Livewire\Users\SysAdmins',
            'html_tag'     => '<livewire:users.sysadmins.user-index-table/>',
            'created_path' => 'app/Livewire/Users/SysAdmins/UserIndexTable.php',
        ]],
        'SYSADMINS' => [[
            'name'         => 'Users\SYSADMINS\UserIndexTable',
            'fqn'          => 'App\Livewire\Users\SYSADMINS\UserIndexTable',
            'namespace'    => 'App\Livewire\Users\SYSADMINS',
            'html_tag'     => '<livewire:users.sysadmins.user-index-table/>',
            'created_path' => 'app/Livewire/Users/SYSADMINS/UserIndexTable.php',
        ]],
        'sys_admins' => [[
            'name'         => 'Users\sysadmins\UserIndexTable',
            'fqn'          => 'App\Livewire\Users\sysadmins\UserIndexTable',
            'namespace'    => 'App\Livewire\Users\sysadmins',
            'html_tag'     => '<livewire:users.sysadmins.user-index-table/>',
            'created_path' => 'app/Livewire/Users/sysadmins/UserIndexTable.php',
        ]],
        'sys-admins' => [[
            'name'         => 'Users\sys-admins\UserIndexTable',
            'fqn'          => 'App\Livewire\Users\sysadmins\UserIndexTable',
            'namespace'    => 'App\Livewire\Users\sysadmins',
            'html_tag'     => '<livewire:users.sysadmins.user-index-table/>',
            'created_path' => 'app/Livewire/Users/sysadmins/UserIndexTable.php',
        ]],
        'sysadmins' => [[
            'name'         => 'Users\sysadmins\UserIndexTable',
            'fqn'          => 'App\Livewire\Users\sysadmins\UserIndexTable',
            'namespace'    => 'App\Livewire\Users\sysadmins',
            'html_tag'     => '<livewire:users.sysadmins.user-index-table/>',
            'created_path' => 'app/Livewire/Users/sysadmins/UserIndexTable.php',
        ]],
    ],
);

test('Livewire class namespace is App\Livewire')
  ->expect(fn () => config('livewire.class_namespace'))->toBe('App\Livewire');

it('can create component in a custom Livewire namespace', function () {
    app()->config->set('livewire.class_namespace', 'Domains');

    $component = PowerGridComponentMaker::make('System/Office/Users/Admin/Active/ListTable');

    expect($component)
      ->name->toBe('ListTable')
      ->namespace->toBe('Domains\System\Office\Users\Admin\Active')
      ->folder->toBe('System\Office\Users\Admin\Active')
      ->fqn->toBe('Domains\System\Office\Users\Admin\Active\ListTable')
      ->htmlTag->toBe('<livewire:system.office.users.admin.active.list-table/>');

    expect($component->createdPath())->toBe("Domains/System/Office/Users/Admin/Active/ListTable.php");

    expect($component->savePath($component->filename))->toEndWith(str_replace('/', DIRECTORY_SEPARATOR, 'Domains/System/Office/Users/Admin/Active/ListTable.php'));
});
