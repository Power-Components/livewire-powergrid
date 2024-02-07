<?php

use Illuminate\Support\Facades\File;

use Laravel\Prompts\{Key, Prompt};
use PowerComponents\LivewirePowerGrid\Actions\ListModels;

beforeEach(function () {
    File::cleanDirectory(base_path('app/Models'));

    $this->artisan('make:model Demo');
});

test('list models', function () {
    expect(ListModels::handle())->toBe([
        'Demo',
        'App\Models\Demo',
    ]);
});
