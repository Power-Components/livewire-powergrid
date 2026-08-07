<?php

use Laravel\Prompts\{Key, Prompt};
use PowerComponents\LivewirePowerGrid\Commands\Actions\AskColumnSource;

test('selecting the model $fillable as field source', function () {
    Prompt::fake([Key::ENTER]);

    expect(AskColumnSource::handle('Dish', 'dishes'))->toBe('FILLABLE');
});

test('selecting the database table as field source', function () {
    Prompt::fake([Key::DOWN, Key::ENTER]);

    expect(AskColumnSource::handle('Dish', 'dishes'))->toBe('DATABASE_TABLE');
});
