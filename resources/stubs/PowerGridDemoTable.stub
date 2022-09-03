<?php

/*
|****************************************************************************************************************
|                               ⚡ PowerGrid Demo Table ⚡
|****************************************************************************************************************
| Table View: resources/views/powergrid-demo.blade.php
| USAGE:
| ➤ You must include Route::view('/powergrid', 'powergrid-demo'); in routes/web.php file.
| ➤ Visit http://your-app/powergrid. Enjoy it!
|****************************************************************************************************************
*/

namespace App\Http\Livewire;

use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Support\{Carbon, Collection};
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    Exportable,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent,
    Rules\RuleActions};

final class PowerGridDemoTable extends PowerGridComponent
{
    use ActionButton;

    // Demo users. Should be removed in a real project.
    protected $demoUsers = null;

    /**
     * User name
     *
     * @var array<int, string> $name
     */
    public array $name;

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'rowActionEvent',
                'bulkActionEvent',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */
    public function rowActionEvent(array $data): void
    {
        $message = 'You have clicked #' . $data['id'];

        $this->dispatchBrowserEvent('showAlert', ['message' => $message]);
    }

    public function bulkActionEvent(): void
    {
        if (count($this->checkboxValues) == 0) {
            $this->dispatchBrowserEvent('showAlert', ['message' => 'You must select at least one item!']);

            return;
        }

        $ids = implode(', ', $this->checkboxValues);

        $this->dispatchBrowserEvent('showAlert', ['message' => 'You have selected IDs: ' . $ids]);
    }

    /*
    |--------------------------------------------------------------------------
    |  Features Setup
    |--------------------------------------------------------------------------
    | Setup Table's general features
    |
    */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    |  Datasource
    |--------------------------------------------------------------------------
    | Provides data to your Table using a Model or Collection
    |
    */

    /**
    * PowerGrid datasource.
    *
    */
    public function datasource(): Collection
    {
        return $this->demoUsers(); //Get demo users. Should be removed in a real project.
        //return User::query();
    }

    /*
    |--------------------------------------------------------------------------
    |  Relationship Search
    |--------------------------------------------------------------------------
    | Configure here relationships to be used by the Search and Table Filters.
    |
    */

    /**
     * Relationship search.
     *
     * @return array<string, array<int, string>>
     */
    public function relationSearch(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    |  Add Column
    |--------------------------------------------------------------------------
    | Make Datasource fields available to be used as columns.
    | You can pass a closure to transform/modify the data.
    |
    | ❗ IMPORTANT: When using closures, you must escape any value coming from
    |    the database using the `e()` Laravel Helper function.
    |
    */
    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('email')

            // Create a custom column with name in lower case.
            ->addColumn('name_lower', function (User $model) {
                return strtolower(e($model->name));
            })

            // Create a custom column "laracon" based on "has_laracon_ticket" boolean field
            ->addColumn('laracon', function (User $model) {
                return ($model->has_laracon_ticket ? 'yes' : 'no');
            })

            //Create a custom column "laravel_since_formatted" for humans, based on "laravel_since"
            ->addColumn('laravel_since_formatted', function (User $model) {
                return Carbon::parse($model->laravel_since)->format('d/m/Y H:i');
            });
    }

    /*
    |--------------------------------------------------------------------------
    |  Table header
    |--------------------------------------------------------------------------
    | Configure Action Buttons for your table header.
    |

    */
     /**
     * PowerGrid Header
     *
     * @return array<int, Button>
     */
    public function header(): array
    {
        return [
            Button::add('bulk-demo')
                ->caption(__('Bulk Action'))
                ->class('cursor-pointer block bg-indigo-500 text-white border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-600 dark:border-gray-500 dark:bg-gray-500 2xl:dark:placeholder-gray-300 dark:text-gray-200 dark:text-gray-300')
                ->emit('bulkActionEvent', [])
        ];
    }

    /*
    |--------------------------------------------------------------------------
    |  Include Columns
    |--------------------------------------------------------------------------
    | Include the columns added columns, making them visible on the Table.
    | Each column can be configured with properties, filters, actions...
    |
    */
    public function columns(): array
    {
        //User permissions. In a real world project, this would be managed by your system.
        $canEdit = true;
        $canCopy = true;

        return [
            Column::make('ID', 'id')
                ->makeInputRange(), //Filter for min-max Range

            Column::make('Full name', 'name')
                ->sortable() //Adds sorting button to the header
                ->searchable() //Includes column in search option (top page)
                ->makeInputText()//Filter for searching text
                ->editOnClick($canEdit), //Allows user to edit information on click

            Column::make('Email address', 'email')
                ->searchable()
                ->makeInputText()
                ->clickToCopy($canCopy), //Button for copy to clipboard

            Column::make('US State', 'state_in_usa')
                ->makeInputSelect($this->demoUsers(), 'state_in_usa', 'id'), //Multiselect filter

            Column::make('Laracon visitor?', 'laracon')
                ->sortable()
                ->makeBooleanFilter('has_laracon_ticket', 'yes', 'no'), //Boolean filter based on "has_laracon_ticket".


            Column::make('Laravel user since', 'laravel_since_formatted')
                ->sortable()
                ->makeInputDatePicker('laravel_since') //Date filter
            ];
    }

    /*
    |--------------------------------------------------------------------------
    | Actions Method
    |--------------------------------------------------------------------------
    | Enable the method below only if the Routes below are defined in your app.
    |
    */

     /**
     * PowerGrid action buttons.
     *
     * @return array<int, Button>
     */

    public function actions(): array
    {
       return [
           Button::make('info', 'Click me')
               ->class('bg-indigo-500 hover:bg-indigo-600 cursor-pointer text-white px-3 py-2 text-sm rounded-md')
               ->emit('rowActionEvent', ['id' => 'id']),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Actions Rules
    |--------------------------------------------------------------------------
    | Enable the method below to configure Rules for your Table and Action Buttons.
    |
    */

    /**
    * PowerGrid action rules.
    *
    * @return array<int, RuleActions>
    */
    public function actionRules(): array
    {
        return [
            //Hide "info" button for row with user ID 1
            Rule::button('info')
                ->when(fn($user) => $user->id === 1)
                ->hide(),

            //Disable "info" button for row with user ID 2
            Rule::button('info')
                ->when(fn($user) => $user->id === 2)
                ->caption('Click me (disabled)')
                ->disable(),

            //Change "info" button caption for row with user ID 3
            Rule::button('info')
                ->when(fn($user) => $user->id === 3)
                ->caption('Click me! 🤠'),

            //Change "background" for row with user ID 4
            Rule::rows()
                ->when(fn($user) => $user->id === 4)
                ->setAttribute('class', 'bg-blue-200 hover:bg-blue-300'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ❗ Blocked update
    |--------------------------------------------------------------------------
    | Data update is blocked on demo to protect your database.

    */
    public function onUpdatedEditable(string $id, string $field, string $value): void
    {
        throw new \Exception(' Data update is blocked on demo to protect your database!');
    }

    /*
    |--------------------------------------------------------------------------
    | ❗ Demo Users (Should be removed in a real project)
    |--------------------------------------------------------------------------
    | Generate random  users for demo purpose.
    | Data will NOT be stored in your database.
    */

    /**
     * Generate demo users
     *
     * @return Collection
     */
    protected function demoUsers(): Collection
    {
        if (!is_null($this->demoUsers)) {
            return $this->demoUsers;
        }

        // Generate demo users.
        // These users will not be saved in your database.

        $faker = FakerFactory::create();

        $users = collect();

        for ($i=1; $i <= 20; $i++) {
            $user = new User([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => (boolval(rand(0, 1)) === true ? now() : null),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => 'hKNojklraZ'
            ]);

            $user->setAttribute('id', $i);
            $user->setAttribute('state_in_usa', $faker->stateAbbr());
            $user->setAttribute('has_laracon_ticket', boolval(rand(0, 1)));
            $user->setAttribute('laravel_since', $faker->dateTimeBetween('-1 year', now()));

            $users->add($user);
        }

        $this->demoUsers = $users;

        return $users;
    }

    public function template(): ?string
    {
        return null;
    }
}
