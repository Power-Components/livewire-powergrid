<?php

use Laravel\Prompts\{Key, Prompt};

use PowerComponents\LivewirePowerGrid\Actions\AskDatabaseTableName;

test('input component name', function () {
    Prompt::fake(['us', Key::DOWN, Key::ENTER]);
    expect(AskDatabaseTableName::handle())->toBe('users');
})->skip('why takes so long?');
