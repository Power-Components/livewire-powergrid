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
use Illuminate\Support\Carbon;
use Faker\Factory as FakerFactory;
use Illuminate\Database\QueryException;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridEloquent;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\Rules\Rule;

final class PowerGridDemoTable extends PowerGridComponent
{
    use ActionButton;

    //Messages informing success/error data is updated.
    public bool $showUpdateMessages = true;

     // Demo users. Should be removed in a real project.
    protected $demoUsers = null;

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(), [
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

    public function setUp(): void
    {
        $this->showCheckBox() //Adds checkboxes to each table row
            ->showRecordCount() //Display: Showing 1 to 10 of 20 Results
            ->showPerPage() //Shows per page option
            ->showSearchInput() //Show search input on page top.
            ->showExportOption('download', ['excel', 'csv']); //Enables export feature and show button on page top.
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
    public function datasource(): \Illuminate\Support\Collection
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
    */
    public function addColumns(): ?PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('email')

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
     * @return array<int, \PowerComponents\LivewirePowerGrid\Button>
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
            Column::add()
                ->title(__('ID'))
                ->field('id')
                ->makeInputRange(), //Filter for min-max Range

            Column::add()
                ->title(__('Full name')) //Colummn header title
                ->field('name') //Reads datasource field 'name'
                ->sortable() //Adds sorting button to the header
                ->searchable() //Includes column in search option (top page)
                ->makeInputText()//Filter for searching text
                ->editOnClick($canEdit), //Allows user to edit information on click

            Column::add()
                ->title(__('E-mail address'))
                ->field('email')
                ->searchable()
                ->makeInputText()
                ->clickToCopy($canCopy), //Button for copy to clipboard

            Column::add()
                ->title(__('US State'))
                ->field('state_in_usa')
                ->makeInputSelect($this->demoUsers(), 'state_in_usa', 'id'), //Multiselect filter

            Column::add()
                ->title(__('Laracon visitor?'))
                ->field('laracon')
                ->searchable()
                ->sortable()
                ->makeBooleanFilter('has_laracon_ticket', 'yes', 'no'), //Boolean filter based on "has_laracon_ticket".


            Column::add()
                ->title(__('Laravel user since'))
                ->field('laravel_since_formatted')
                ->searchable()
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
     * @return array<int, \PowerComponents\LivewirePowerGrid\Button>
     */

    public function actions(): array
    {
       return [
           Button::add('info')
               ->caption(__('Click me'))
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
    * @return array<int, \PowerComponents\LivewirePowerGrid\Rules\RuleActions>
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
    | Edit Method
    |--------------------------------------------------------------------------
    | Enable the method below to use editOnClick() or toggleable() methods
    |
    */

     /**
     * PowerGrid Update.
     *
     * @param array<string,string> $data
     */

    public function update(array $data): bool
    {
        /*
        |--------------------------------------------------------------------------
        | ❗ Forcing update to fail ❗
        |--------------------------------------------------------------------------
        | Update will not be performed in order to protect your data.
        */
        return false;
        //--------------------------------------------------------------------------

        try {
            $updated = user::query()->find($data['id'])->update([
                $data['field'] => $data['value']
           ]);
        } catch (QueryException $exception) {
            $updated = false;
        }

        return $updated;
    }

    public function updateMessages(string $status, string $field = '_default_message'): string
    {
        $updateMessages = [
            'success'   => [
                '_default_message' => __('Data has been updated successfully!'),
                //'custom_field' => __('Custom Field updated successfully!'),
            ],
            'error' => [
                '_default_message' => __('Update is blocked in Demo table!'),
                'name' => __('The update of "name" field is blocked in Demo table!'),
                //'custom_field' => __('Error updating custom field.'),
            ]
        ];

        return ($updateMessages[$status][$field] ?? $updateMessages[$status]['_default_message']);
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
     * @return \Illuminate\Support\Collection
     */
    protected function demoUsers(): \Illuminate\Support\Collection
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
