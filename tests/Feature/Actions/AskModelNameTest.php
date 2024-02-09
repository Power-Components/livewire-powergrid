<?php

use Illuminate\Support\Facades\File;

use Laravel\Prompts\{Key, Prompt};
use PowerComponents\LivewirePowerGrid\Actions\AskModelName;

beforeEach(function () {
    File::cleanDirectory(base_path('app/Models'));

    $this->artisan('make:model Demo');
});

test('input component name', function () {
    Prompt::fake(['D', Key::DOWN, Key::ENTER]);
    expect(AskModelName::handle())->toBe('Demo');
})->skip('Check test');
