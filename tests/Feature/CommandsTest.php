<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->tableFilePath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/app/Http/Livewire/DemoTable.php';
    $this->model_name_question = 'What is the name of your new ⚡ PowerGrid Table (E.g., <comment>UserTable</comment>)?';
    $this->datasource_question = 'Create Datasource with <comment>[M]</comment>odel or <comment>[C]</comment>ollection? (Default: Model)';
    $this->model_path_question = 'Enter your Model path (E.g., <comment>App\Models\User</comment>)';
    $this->use_fillable_question = 'Create columns based on Model\'s <comment>fillable</comment> property?';
});

it('creates a PowerGrid Table', function () {
    File::delete($this->tableFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->model_name_question, 'DemoTable')
        ->expectsQuestion($this->datasource_question, 'M')
        ->expectsQuestion($this->model_path_question, 'PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->use_fillable_question, 'yes')
        ->expectsOutput("\n⚡ DemoTable.php was successfully created at [App/Http/Livewire/].")
        ->expectsOutput("\n⚡ Your PowerGrid can be now included with the tag: <livewire:demo-table/>\n");

    $this->assertFileExists($this->tableFilePath);

    File::delete($this->tableFilePath);
});


it('publishes the Demo Table', function () {
    $tableFile =  __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/app/Http/Livewire/PowerGridDemoTable.php';
    $viewsFile =  __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/resources/views/powergrid-demo.blade.php';

    File::delete($tableFile);
    File::delete($viewsFile);

    $this->artisan('powergrid:demo')
        ->expectsOutput("⚡ *** PowerGrid Demo Table is ready! ***")
        ->expectsOutput("\n⚡ PowerGridDemoTable.php was successfully created at [App/Http/Livewire/]")
        ->expectsOutput("\n⚡ powergrid-demo.blade.php was successfully created at [resources/views/]")
        ->expectsOutput("\n⚡ *** Usage ***")
        ->expectsOutput("\n➤ You must include Route::view('/powergrid', 'powergrid-demo'); in routes/web.php file.")
        ->expectsOutput("\n➤ Visit http://your-app/powergrid. Enjoy it!\n");


    $this->assertFileExists($tableFile);
    $this->assertFileExists($viewsFile);

    File::delete($tableFile);
    File::delete($viewsFile);
});


it('does not accept an empty table name', function () {
    File::delete($this->tableFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->model_name_question, '')
        ->expectsOutput('You must provide a name for your ⚡ PowerGrid Table!')
        ->assertExitCode(0);
    
    $this->assertFileDoesNotExist($this->tableFilePath);
});

it('accepts only [M]odel or [C]ollection', function () {
    File::delete($this->tableFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->model_name_question, 'DemoTable')
        ->expectsQuestion($this->datasource_question, 'Z')
        ->expectsOutput('Please enter [M] for Model or [C] for Collection')
        ->assertExitCode(0);

    $this->assertFileDoesNotExist($this->tableFilePath);
});

it('does not create a table with empty model', function () {
    File::delete($this->tableFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->model_name_question, 'DemoTable')
        ->expectsQuestion($this->datasource_question, 'M')
        ->expectsQuestion($this->model_path_question, '')
        ->expectsOutput('Error: Model name is required.')
        ->assertExitCode(0);

    $this->assertFileDoesNotExist($this->tableFilePath);

    File::delete($this->tableFilePath);
});

it('does not create a table with invalid model path', function () {
    File::delete($this->tableFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->model_name_question, 'DemoTable')
        ->expectsQuestion($this->datasource_question, 'M')
        ->expectsQuestion($this->model_path_question, 'xyz-model')
        ->expectsQuestion($this->use_fillable_question, 'yes')
        ->expectsOutput('Error: "xyz-model" Invalid model path. Path must be like: "\App\Models\User"')
        ->assertExitCode(0);

    $this->assertFileDoesNotExist($this->tableFilePath);

    File::delete($this->tableFilePath);
});

it('does overwride the existing table file w/ YES', function () {
    File::delete($this->tableFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->model_name_question, 'DemoTable')
        ->expectsQuestion($this->datasource_question, 'M')
        ->expectsQuestion($this->model_path_question, '\PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->use_fillable_question, 'yes');

    $this->assertFileExists($this->tableFilePath);

    //Add some content to file
    file_put_contents($this->tableFilePath, 'x0007');
    
    //Alert about overwrite
    $this->artisan('powergrid:create')
        ->expectsQuestion($this->model_name_question, 'DemoTable')
        ->expectsQuestion($this->datasource_question, 'M')
        ->expectsQuestion($this->model_path_question, '\PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->use_fillable_question, 'yes')
        ->expectsQuestion('It seems that <comment>DemoTable</comment> already exists. Would you like to overwrite it?', 'yes');


    expect(file_get_contents($this->tableFilePath))->not->toContain('x0007');

    File::delete($this->tableFilePath);
});

it('does NOT overwride the existing table file', function () {
    File::delete($this->tableFilePath);

    $this->artisan('powergrid:create')
        ->expectsQuestion($this->model_name_question, 'DemoTable')
        ->expectsQuestion($this->datasource_question, 'M')
        ->expectsQuestion($this->model_path_question, '\PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->use_fillable_question, 'yes');

    $this->assertFileExists($this->tableFilePath);

    //Add some content to file
    file_put_contents($this->tableFilePath, 'x0007');
    
    //Alert about overwrite
    $this->artisan('powergrid:create')
        ->expectsQuestion($this->model_name_question, 'DemoTable')
        ->expectsQuestion($this->datasource_question, 'M')
        ->expectsQuestion($this->model_path_question, '\PowerComponents\LivewirePowerGrid\Tests\Models\Dish')
        ->expectsQuestion($this->use_fillable_question, 'yes')
        ->expectsQuestion('It seems that <comment>DemoTable</comment> already exists. Would you like to overwrite it?', '');

    expect(file_get_contents($this->tableFilePath))->toContain('x0007');

    File::delete($this->tableFilePath);
});


it('publishes config file', function () {
    $configFilePath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/config/livewire-powergrid.php';

    File::delete($configFilePath);

    $this->artisan('vendor:publish --tag=livewire-powergrid-config')
        ->expectsOutput('Publishing complete.');

    $this->assertFileExists($configFilePath);

    File::delete($configFilePath);
});

it('publishes views file', function () {
    $dirPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/resources/views/vendor/livewire-powergrid';

    File::delete($dirPath);

    $this->artisan('vendor:publish --tag=livewire-powergrid-views')
        ->expectsOutput('Publishing complete.');

    $this->assertDirectoryExists($dirPath);

    File::delete($dirPath);
});

it('publishes lang file', function () {
    $dirPath = __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/resources/lang/vendor/livewire-powergrid';

    File::delete($dirPath);

    $this->artisan('vendor:publish --tag=livewire-powergrid-lang')
        ->expectsOutput('Publishing complete.');

    $this->assertDirectoryExists($dirPath);

    File::delete($dirPath);
});
