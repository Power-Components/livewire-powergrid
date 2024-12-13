<?php

use Illuminate\Support\Facades\Route;
use PowerComponents\LivewirePowerGrid\Controllers\InjectJsController;

Route::name('powergrid.')->prefix('/__powergrid')->group(function () {
    Route::get('js', InjectJsController::class)->name('global.js');
});
