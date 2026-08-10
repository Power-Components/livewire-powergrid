<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the `waiters` table backing the column generator tests.
 *
 * It deliberately mixes columns that are fillable, hidden, sensitive and
 * database-only, so the different field sources can be told apart.
 */
function createWaitersTable(): void
{
    Schema::dropIfExists('waiters');

    Schema::create('waiters', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->string('password');
        $table->string('remember_token')->nullable();
        $table->string('internal_note')->nullable();
        $table->integer('tips');
        $table->date('hired_at');
        $table->timestamps();
    });
}
