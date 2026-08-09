<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use PowerComponents\LivewirePowerGrid\Commands\Support\SchemaColumns;

beforeEach(function () {
    Schema::dropIfExists('schema_probe');

    Schema::create('schema_probe', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->char('initials', 2);
        $table->text('description');
        $table->uuid('external_id');
        $table->enum('status', ['draft', 'published']);
        $table->integer('votes');
        $table->bigInteger('views');
        $table->smallInteger('rank');
        $table->tinyInteger('stars');
        $table->boolean('is_active');
        $table->date('published_on');
        $table->dateTime('reviewed_at');
        $table->timestamp('archived_at')->nullable();
        $table->decimal('price', 8, 2);
        $table->double('weight');
        $table->json('payload')->nullable();
    });
});

afterEach(function () {
    Schema::dropIfExists('schema_probe');
});

it('normalizes native column types into generic buckets', function (string $column, string $bucket) {
    expect(SchemaColumns::handle('schema_probe')->get($column))->toBe($bucket);
})->with([
    'auto increment key' => ['id', 'integer'],
    'varchar' => ['name', 'string'],
    'char' => ['initials', 'string'],
    'text' => ['description', 'string'],
    'uuid' => ['external_id', 'string'],
    'enum' => ['status', 'string'],
    'integer' => ['votes', 'integer'],
    'big integer' => ['views', 'integer'],
    'small integer' => ['rank', 'integer'],
    'tiny integer' => ['stars', 'integer'],
    'boolean' => ['is_active', 'boolean'],
    'date' => ['published_on', 'date'],
    'datetime' => ['reviewed_at', 'datetime'],
    'timestamp' => ['archived_at', 'datetime'],
    'decimal' => ['price', 'other'],
    'double' => ['weight', 'other'],
]);

it('tells a boolean apart from a tiny integer', function () {
    // MySQL and SQLite report both as `tinyint`; only the display width differs.
    expect(SchemaColumns::handle('schema_probe'))
        ->get('is_active')->toBe('boolean')
        ->get('stars')->toBe('integer');
});

it('normalizes json to the type the driver actually stores it as', function () {
    // SQLite has no json type: the column is created as text.
    $expected = DB::getDriverName() === 'sqlite' ? 'string' : 'other';

    expect(SchemaColumns::handle('schema_probe')->get('payload'))->toBe($expected);
});

it('keeps the table column order', function () {
    expect(SchemaColumns::handle('schema_probe')->keys()->take(5)->all())
        ->toBe(['id', 'name', 'initials', 'description', 'external_id']);
});

it('reads a table on a named connection', function () {
    expect(SchemaColumns::handle('schema_probe', 'testbench')->get('name'))->toBe('string');
});

it('returns no columns when the table does not exist', function () {
    // A component may be generated before the database is migrated.
    expect(SchemaColumns::handle('table_that_does_not_exist'))->toBeEmpty();
});

it('returns no columns when the connection is unusable', function () {
    config()->set('database.connections.broken', [
        'driver' => 'sqlite',
        'database' => '/does/not/exist/powergrid.sqlite',
        'prefix' => '',
    ]);

    expect(SchemaColumns::handle('schema_probe', 'broken'))->toBeEmpty();
});

it('rejects sensitive columns from the public field list', function (string $column) {
    $types = collect(['id' => 'integer', $column => 'string', 'name' => 'string']);

    expect(SchemaColumns::publicFields($types))->toBe(['id', 'name']);
})->with(SchemaColumns::SENSITIVE_COLUMNS);

it('keeps every other column in table order', function () {
    $types = SchemaColumns::handle('schema_probe');

    expect(SchemaColumns::publicFields($types))->toBe($types->keys()->all());
});

it('only rejects an exact sensitive column name', function () {
    $types = collect(['password_changed_at' => 'datetime', 'user_password' => 'string']);

    expect(SchemaColumns::publicFields($types))->toBe(['password_changed_at', 'user_password']);
});
