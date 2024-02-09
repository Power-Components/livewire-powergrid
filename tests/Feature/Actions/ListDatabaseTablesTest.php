<?php

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;

use PowerComponents\LivewirePowerGrid\Actions\ListDatabaseTables;

test('list database tables except hidden ones', function () {
    Schema::dropAllTables();

    collect(['failed_jobs', 'migrations', 'password_reset_tokens', 'personal_access_tokens', 'dishes', 'restaurants'])->each(function ($name) {
        Schema::create($name, function (Blueprint $table) {
            $table->id();
        });
    });

    expect(ListDatabaseTables::handle())->toEqualCanonicalizing(['dishes', 'restaurants']);
});
