<?php

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;
use Laravel\Prompts\{Key, Prompt};

use PowerComponents\LivewirePowerGrid\Actions\AskDatabaseTableName;

test('input component name', function () {
    Schema::dropAllTables();

    Schema::create('foobar', function (Blueprint $table) {
        $table->id();
    });

    Prompt::fake(['foo', Key::DOWN, Key::ENTER]);

    expect(AskDatabaseTableName::handle())->toBe('foobar');
})->skip('Check test: it gets stuck');
