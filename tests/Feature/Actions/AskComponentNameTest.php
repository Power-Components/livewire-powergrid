<?php

use Laravel\Prompts\{Key, Prompt};
use PowerComponents\LivewirePowerGrid\Commands\Actions\AskComponentName;

test('input component name', function () {
    Prompt::fake([...str_split('New'), Key::ENTER]);
    expect(AskComponentName::handle())->toBe('UserTableNew');
})->skip('Check test');
