<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->tableModelFilePath      = getLaravelDir() . 'app/Http/Livewire/DemoTable.php';
    $this->tableCollectionFilePath = getLaravelDir() . 'app/Http/Livewire/CollectionTable.php';
    $this->modelNameQuestion       = 'What is the name of your Table Component? (E.g., <comment>UserTable</comment>)';
    $this->tableNameQuestion       = 'What is the name of your database table name? (E.g., <comment>users</comment>)';
    $this->datasourceAnswer        = 'What type of data source will you use?';
    $this->datasourceAnswers       = [
        'Eloquent Builder (Model)',
        'Query Builder (DB::table(\'table_name\'))',
        'Collection',
    ];
    $this->modelPathQuestion   = 'Enter your Builder name or file path (E.g., <comment>User</comment> or <comment>App\Models\User</comment>)';
    $this->useFillableQuestion = 'Create columns based on Model\'s <comment>fillable</comment> property?';
});

it('creates a PowerGrid Builder Table using Eloquent Builder', function () {
    File::delete($this->tableModelFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'DemoTable')
        ->expectsChoice($this->datasourceAnswer, 'Eloquent Builder (Model)', $this->datasourceAnswers, true)
        ->expectsQuestion($this->modelPathQuestion, 'PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->useFillableQuestion, 'yes')
        ->expectsOutput("\n⚡ DemoTable.php was successfully created at [App/Http/Livewire/].")
        ->expectsOutput("\n⚡ Your PowerGrid table can be now included with the tag: <livewire:demo-table/>")
        ->assertSuccessful();

    $this->assertFileExists($this->tableModelFilePath);

    File::delete($this->tableModelFilePath);
});

it('creates a PowerGrid Builder Table using Query Builder', function () {
    File::delete($this->tableModelFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'DemoTable')
        ->expectsChoice($this->datasourceAnswer, 'Query Builder (DB::table(\'table_name\'))', $this->datasourceAnswers, true)
        ->expectsQuestion($this->useFillableQuestion, 'yes')
        ->expectsQuestion($this->tableNameQuestion, 'users')
        ->expectsOutput("\n⚡ DemoTable.php was successfully created at [App/Http/Livewire/].")
        ->expectsOutput("\n⚡ Your PowerGrid table can be now included with the tag: <livewire:demo-table/>")
        ->assertSuccessful();

    $this->assertFileExists($this->tableModelFilePath);

    File::delete($this->tableModelFilePath);
});

it('creates a PowerGrid Collection Table', function () {
    File::delete($this->tableCollectionFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'CollectionTable')
        ->expectsChoice($this->datasourceAnswer, 'Collection', $this->datasourceAnswers, true)
        ->expectsOutput("\n⚡ CollectionTable.php was successfully created at [App/Http/Livewire/].")
        ->expectsOutput("\n⚡ Your PowerGrid table can be now included with the tag: <livewire:collection-table/>")
        ->assertSuccessful();

    $this->assertFileExists($this->tableCollectionFilePath);

    File::delete($this->tableCollectionFilePath);
});

it('notifies about tailwind forms', function () {
    File::delete($this->tableModelFilePath);

    $tailwindConfigFile = base_path() . '/' . 'tailwind.config.js';

    $content = "module.exports = { theme: { // ... }, plugins: [ require('@tailwindcss/forms'), // ... ], }";

    File::delete($tailwindConfigFile);
    File::put($tailwindConfigFile, $content);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'DemoTable')
        ->expectsChoice('What type of data source will you use?', 'Eloquent Builder (Model)', [
            'Eloquent Builder (Model)',
            'Query Builder (DB::table(\'table_name\'))',
            'Collection',
        ], true)
        ->expectsQuestion($this->modelPathQuestion, 'PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->useFillableQuestion, 'yes')
        ->expectsOutput("\n💡 It seems you are using the plugin Tailwindcss/form.\n   Please check: https://livewire-powergrid.com/#/get-started/configure?id=_43-tailwind-forms for more information.")
        ->expectsOutput("\n⚡ DemoTable.php was successfully created at [App/Http/Livewire/].")
        ->expectsOutput("\n⚡ Your PowerGrid table can be now included with the tag: <livewire:demo-table/>")
        ->assertSuccessful();

    File::delete($this->tableModelFilePath);
    File::delete($tailwindConfigFile);
});

it('publishes the Demo Table', function () {
    $tableFile = getLaravelDir() . 'app/Http/Livewire/PowerGridDemoTable.php';
    $viewsFile = getLaravelDir() . 'resources/views/powergrid-demo.blade.php';

    File::delete($tableFile);
    File::delete($viewsFile);

    $this->artisan('powergrid:demo')
        ->expectsOutput("➤ PowerGridDemoTable.php was successfully created at [App/Http/Livewire/]\n")
        ->expectsOutput("➤ powergrid-demo.blade.php was successfully created at [resources/views/]\n")
        ->expectsOutput("\n1. You must include Route::view('/powergrid', 'powergrid-demo'); in your routes/web.php file.")
        ->expectsOutput("\n2. Serve your project. For example, run php artisan serve.")
        ->expectsOutput("\n3. Visit http://localhost/powergrid to view the Demo Table.")
        ->expectsOutput("\n\n⭐ Thanks! Please consider starring our repository at https://github.com/Power-Components/livewire-powergrid ⭐\n")
        ->assertSuccessful();

    $this->assertFileExists($tableFile);
    $this->assertFileExists($viewsFile);

    File::delete($tableFile);
    File::delete($viewsFile);
});

it('does not accept an empty table name', function () {
    File::delete($this->tableModelFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, '')
        ->expectsOutput('You must provide a name for your ⚡ PowerGrid Table!')
        ->assertFailed();

    $this->assertFileDoesNotExist($this->tableModelFilePath);
});

it('does not create a table with empty model', function () {
    File::delete($this->tableModelFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'DemoTable')
        ->expectsChoice($this->datasourceAnswer, 'Eloquent Builder (Model)', $this->datasourceAnswers, true)
        ->expectsQuestion($this->modelPathQuestion, '')
        ->expectsOutput('Error: You must inform the Builder name or file path.')
        ->assertFailed();

    $this->assertFileDoesNotExist($this->tableModelFilePath);

    File::delete($this->tableModelFilePath);
});

it('does not create a table with invalid model path', function () {
    File::delete($this->tableModelFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'DemoTable')
        ->expectsChoice($this->datasourceAnswer, 'Eloquent Builder (Model)', $this->datasourceAnswers, true)
        ->expectsQuestion($this->modelPathQuestion, 'xyz-model')
        ->expectsOutput('Error: Could not find "xyz-model" class.')
        ->assertFailed();

    $this->assertFileDoesNotExist($this->tableModelFilePath);

    File::delete($this->tableModelFilePath);
});

it('does overwrite the existing table file w/ YES', function () {
    File::delete($this->tableModelFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'DemoTable')
        ->expectsChoice($this->datasourceAnswer, 'Eloquent Builder (Model)', $this->datasourceAnswers, true)
        ->expectsQuestion($this->modelPathQuestion, '\PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->useFillableQuestion, 'yes')
        ->assertSuccessful();

    $this->assertFileExists($this->tableModelFilePath);

    //Add some content to file
    file_put_contents($this->tableModelFilePath, 'x0007');

    //Alert about overwrite
    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'DemoTable')
        ->expectsChoice($this->datasourceAnswer, 'Eloquent Builder (Model)', $this->datasourceAnswers, true)
        ->expectsQuestion($this->modelPathQuestion, '\PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->useFillableQuestion, 'yes')
        ->expectsQuestion('It seems that <comment>DemoTable</comment> already exists. Would you like to overwrite it?', 'yes')
        ->assertSuccessful();

    expect(file_get_contents($this->tableModelFilePath))->not->toContain('x0007');

    File::delete($this->tableModelFilePath);
});

it('does NOT overwride the existing table file', function () {
    File::delete($this->tableModelFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'DemoTable')
        ->expectsChoice($this->datasourceAnswer, 'Eloquent Builder (Model)', $this->datasourceAnswers, true)
        ->expectsQuestion($this->modelPathQuestion, '\PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->useFillableQuestion, 'yes')
        ->assertSuccessful();

    $this->assertFileExists($this->tableModelFilePath);

    //Add some content to file
    file_put_contents($this->tableModelFilePath, 'x0007');

    //Alert about overwrite
    $this->artisan('powergrid:create')
        ->expectsQuestion($this->modelNameQuestion, 'DemoTable')
        ->expectsChoice($this->datasourceAnswer, 'Eloquent Builder (Model)', $this->datasourceAnswers, true)
        ->expectsQuestion($this->modelPathQuestion, '\PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->useFillableQuestion, 'yes')
        ->expectsQuestion('It seems that <comment>DemoTable</comment> already exists. Would you like to overwrite it?', '')
        ->assertSuccessful();

    expect(file_get_contents($this->tableModelFilePath))->toContain('x0007');

    File::delete($this->tableModelFilePath);
});

it('publishes config file', function () {
    $configFilePath = getLaravelDir() . 'config/livewire-powergrid.php';

    File::delete($configFilePath);

    $this->artisan('vendor:publish --tag=livewire-powergrid-config')
        ->assertSuccessful();

    $this->assertFileExists($configFilePath);

    File::delete($configFilePath);
});

it('publishes views file', function () {
    $dirPath = getLaravelDir() . 'resources/views/vendor/livewire-powergrid';

    File::delete($dirPath);

    $this->artisan('vendor:publish --tag=livewire-powergrid-views')
        ->assertSuccessful();

    $this->assertDirectoryExists($dirPath);

    File::delete($dirPath);
});

it('publishes the language file in the lang path', function () {
    $dirPath = lang_path('vendor/livewire-powergrid');

    File::delete($dirPath);

    $this->artisan('vendor:publish --tag=livewire-powergrid-lang')
        ->assertSuccessful();

    $this->assertDirectoryExists($dirPath);

    File::delete($dirPath);
});
