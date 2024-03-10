<?php

use PowerComponents\LivewirePowerGrid\Actions\SanitizeComponentName;

it('can properly sanitize a component name', function (string $componentName, string $result) {
    expect(SanitizeComponentName::handle($componentName))->toBe($result);
})
->with([
    ['List_Table/', 'ListTable'],
    ['List_Table\\', 'ListTable'],
    ['List_Table', 'ListTable'],
    ['List-Table', 'ListTable'],
    ['ListTable', 'ListTable'],
    ['User ListTable', 'User\ListTable'],
    ['User      ListTable', 'User\ListTable'],
    ['User///ListTable', 'User\ListTable'],
    ['User\\\\\ListTable', 'User\ListTable'],
    ['User/ListTable', 'User\ListTable'],
    ['User\ListTable', 'User\ListTable'],
    ['User.ListTable', 'User\ListTable'],
    ['User...ListTable', 'User\ListTable'],
    ['User.Admin.ListTable', 'User\Admin\ListTable'],
    ['List_Table.php', 'ListTable'],
    ['User.Foo', 'User\Foo'],
    ['User\UserTables\ListTable', 'User\UserTables\ListTable'],
    ['User\user-tables\ListTable', 'User\usertables\ListTable'],
    ['User\user-tables\ListTable', 'User\usertables\ListTable'],
    ['User//UserTables//ListTable', 'User\UserTables\ListTable'],
    ['User//user-tables//ListTable', 'User\usertables\ListTable'],
    ['User//user-tables//ListTable', 'User\usertables\ListTable'],
    ['User\tables\ListTable', 'User\tables\ListTable'],
    ['user\tables\ListTable', 'user\tables\ListTable'],
]);
