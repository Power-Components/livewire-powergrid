<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

uses()->group('header');

function headerAlignComponent(string $align): PowerGridComponent
{
    return new class($align) extends PowerGridComponent
    {
        public string $alignArg = 'right';

        public function __construct(string $align = 'right')
        {
            $this->alignArg = $align;
            $this->tableName = 'header-align';
        }

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'A']]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::header()->align($this->alignArg)->showSearchInput(),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };
}

it('defaults the header controls alignment to right', function () {
    expect(PowerGrid::header()->controlsAlign)->toBe('right');
});

it('sets the header controls alignment through left, center and right helpers', function () {
    expect(PowerGrid::header()->left()->controlsAlign)->toBe('left')
        ->and(PowerGrid::header()->center()->controlsAlign)->toBe('center')
        ->and(PowerGrid::header()->right()->controlsAlign)->toBe('right');
});

it('falls back to right for an invalid header alignment', function () {
    expect(PowerGrid::header()->align('nope')->controlsAlign)->toBe('right');
});

it('maps each alignment to its margin utility class', function () {
    $component = headerAlignComponent('left');
    $component->setUp = ['header' => PowerGrid::header()->left()];
    expect($component->headerControlsAlignClass())->toBe('mr-auto');

    $component->setUp = ['header' => PowerGrid::header()->center()];
    expect($component->headerControlsAlignClass())->toBe('mx-auto');

    $component->setUp = ['header' => PowerGrid::header()->right()];
    expect($component->headerControlsAlignClass())->toBe('ml-auto');
});

it('renders the configured alignment class on the controls container', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'header-align-left';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'A']]);
        }

        public function setUp(): array
        {
            return [PowerGrid::header()->left()->showSearchInput()];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    expect(Livewire::test($component::class)->html())->toContain('mr-auto');
});
