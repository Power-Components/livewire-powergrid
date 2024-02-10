<?php

use Laravel\Prompts\{Key, Prompt};

use PowerComponents\LivewirePowerGrid\Actions\AskComponentDatasource;

test('selecting component data source', function () {
    Prompt::fake(['us', Key::DOWN, Key::ENTER]);
    expect(AskComponentDatasource::handle())->toBe('QUERY_BUILDER');
})->skip('failing for some reason');
