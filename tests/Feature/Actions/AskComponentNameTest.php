<?php

use Laravel\Prompts\{Key, Prompt};
use PowerComponents\LivewirePowerGrid\Commands\Actions\AskComponentName;

test('input component name', function () {
    Prompt::fake(['New', Key::ENTER]);
    expect(AskComponentName::handle())->toBe('UserTableNew');
});
