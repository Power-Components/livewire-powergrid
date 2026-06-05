## PowerGrid - Livewire DataTable Component

PowerGrid (`power-components/livewire-powergrid`) is a Laravel Livewire component for building dynamic, feature-rich data tables with minimal boilerplate.

### Core Architecture (v7)

PowerGrid 7.x prioritizes **simplicity, an adaptable native skeleton, and independent test components**.

- **PHP:** 8.3+
- **Livewire:** 4.0+
- **Tailwind CSS:** 4.x+
- **Required Package:** `power-components/partials` for DOM isolation

### Artisan Commands

- Create a PowerGrid component: `php artisan powergrid:create MyTable`

### Key Concepts

#### Component Structure

Every PowerGrid table extends `PowerGridComponent` and defines three core methods:

@verbatim
<code-snippet name="PowerGrid Component" lang="php">
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

class UserTable extends PowerGridComponent
{
    public function datasource(): ?Builder
    {
        return User::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('created_at_formatted', fn ($row) => $row->created_at->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable(),
            Column::make('Created', 'created_at_formatted', 'created_at')->sortable(),
        ];
    }
}
</code-snippet>
@endverbatim

#### Theming System

PowerGrid 7.x uses a unified `struct()` method with a fluent builder pattern for theme tokens. CSS classes are resolved via dot-notation:

@verbatim
<code-snippet name="Theme Token Access" lang="php">
// In Blade views
{{ theme('table.layout.td') }}
{{ theme('header.layout.container') }}
{{ theme_view('pagination') }}
</code-snippet>
@endverbatim

Available themes: `Tailwind` (default), `DaisyUI`, `Flux`.

Theme configuration in `config/livewire-powergrid.php`:

@verbatim
<code-snippet name="Theme Configuration" lang="php">
'theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class,
</code-snippet>
@endverbatim

#### Filters

@verbatim
<code-snippet name="Adding Filters" lang="php">
use PowerComponents\LivewirePowerGrid\Facades\Filter;

public function filters(): array
{
    return [
        Filter::inputText('name')->operators(['contains', 'starts_with']),
        Filter::select('status')->dataSource(collect(['active', 'inactive'])),
        Filter::boolean('is_admin')->label('Admin', 'User'),
        Filter::number('age'),
        Filter::datePicker('created_at'),
    ];
}
</code-snippet>
@endverbatim

#### Actions (Server-Side Rendered)

Actions are fully rendered server-side as Blade components via `renderActions()`. No JavaScript processing or caching.

@verbatim
<code-snippet name="Table Actions" lang="php">
use PowerComponents\LivewirePowerGrid\Button;

public function actions($row): array
{
    return [
        Button::make('edit')
            ->slot('Edit')
            ->class('btn btn-primary')
            ->route('users.edit', ['user' => $row->id]),

        Button::make('delete')
            ->slot('Delete')
            ->class('btn btn-danger')
            ->dispatch('deleteUser', ['id' => $row->id]),
    ];
}
</code-snippet>
@endverbatim

#### Performance & Partials

PowerGrid uses `power-components/partials` fragments to isolate DOM updates via three Hot Zones:
1. `pg-tbody` - Table body updates
2. `pg-pagination` - Pagination updates
3. `pg-filters` - Filter state updates

#### Per-Component Theme Override

@verbatim
<code-snippet name="Custom Theme Per Component" lang="php">
// Swap entire theme class
public function customThemeClass(): ?string
{
    return \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class;
}

// Or merge specific tokens
public function template(): ?Theme
{
    return Tailwind::make()->merge([
        'table' => [
            'layout' => [
                'tr' => 'hover:bg-gray-50 dark:hover:bg-gray-800',
            ],
        ],
    ]);
}
</code-snippet>
@endverbatim

### Views Architecture

- **No Theme Conditionals:** Never use `@if($theme == 'bootstrap')` in Blade files.
- **Micro-files Strategy:** Table views are divided into single-purpose micro-files (`index.blade.php`, `tbody.blade.php`, `td.blade.php`).
- **Directives:** Use `@theme()` directives exclusively.

### Testing Best Practices

- Tests must be self-sufficient and theme-independent.
- Focus on core engine capabilities: Search, Filters, Sort, and Pagination.
- Build mini-components at runtime within test files (no global fixtures).

@verbatim
<code-snippet name="PowerGrid Test Pattern" lang="php">
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

test('can search by name', function () {
    $component = new class extends PowerGridComponent {
        public function datasource(): ?Builder
        {
            return User::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name')->searchable(),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});
</code-snippet>
@endverbatim

### Plugins

PowerGrid supports external plugins for custom column behaviors (e.g., selectable, colorpicker, rating). Plugins are created in the user's project at `app/PowerGrid/Plugins/{PluginName}/` and extend `PluginBase`.

@verbatim
<code-snippet name="Plugin Column Macro" lang="php">
// After registering a plugin, use it in columns:
Column::make('Status', 'status')->selectable(['active', 'inactive', 'pending']);
</code-snippet>
@endverbatim

### Configuration

Key configuration options in `config/livewire-powergrid.php`:

- `theme` - Default theme class
- `plugins` - Registered plugin classes
- `pagination` - Default pagination type and per-page options
