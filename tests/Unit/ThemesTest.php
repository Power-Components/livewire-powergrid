<?php

use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, DaisyUI, Tailwind};

describe('Theme: Tailwind', function () {
    it('has a non-empty name property', function () {
        $theme = new Tailwind();

        expect($theme->name)
            ->toBeString()
            ->not->toBeEmpty()
            ->toBe('tailwind');
    });

    it('returns root path and contains base namespace', function () {
        $theme = new Tailwind();

        expect($theme->root())
            ->toBeString()
            ->toContain('livewire-powergrid::components.frameworks')
            ->toBe('livewire-powergrid::components.frameworks.tailwind');
    });

    it('returns full table configuration', function () {
        $table = (new Tailwind())->table();

        expect($table)
            ->toBeArray()
            ->toHaveKeys(['layout', 'header', 'body'])
            ->and($table['layout'])->toHaveKeys(['base', 'div', 'table', 'container', 'actions'])
            ->and($table['header'])->toHaveKeys(['thead', 'tr', 'th', 'thAction'])
            ->and($table['body'])->toHaveKeys(['tbody', 'tr', 'td']);
    });

    it('returns column configuration', function () {
        expect((new Tailwind())->cols())
            ->toBeArray()
            ->toHaveKey('div');
    });

    it('returns footer configuration', function () {
        expect((new Tailwind())->footer())
            ->toBeArray()
            ->toHaveKeys(['view', 'select', 'footer']);
    });

    it('toggleable contains only view', function () {
        expect((new Tailwind())->toggleable())
            ->toBeArray()
            ->toHaveKey('view')
            ->toHaveCount(1);
    });

    it('returns editable configuration', function () {
        expect((new Tailwind())->editable())
            ->toBeArray()
            ->toHaveKeys(['view', 'input']);
    });

    it('returns checkbox configuration', function () {
        expect((new Tailwind())->checkbox())
            ->toBeArray()
            ->toHaveKeys(['th', 'base', 'label', 'input']);
    });

    it('returns radio configuration', function () {
        expect((new Tailwind())->radio())
            ->toBeArray()
            ->toHaveKeys(['th', 'base', 'label', 'input']);
    });

    it('returns all filter configurations', function () {
        $theme = new Tailwind();

        expect($theme->filterBoolean())->toHaveKeys(['view', 'base', 'select'])
            ->and($theme->filterDatePicker())->toHaveKeys(['base', 'view', 'input'])
            ->and($theme->filterMultiSelect())->toHaveKeys(['view', 'base', 'select'])
            ->and($theme->filterNumber())->toHaveKeys(['view', 'input'])
            ->and($theme->filterSelect())->toHaveKeys(['view', 'base', 'select'])
            ->and($theme->filterInputText())->toHaveKeys(['view', 'base', 'select', 'input']);
    });

    it('returns searchbox with all required keys', function () {
        $searchBox = (new Tailwind())->searchBox();

        expect($searchBox)
            ->toBeArray()
            ->toHaveKey('input')
            ->toHaveKey('iconSearch')
            ->toHaveKey('iconClose')
            ->and($searchBox['iconSearch'])->toContain('text-pg-primary-300')
            ->and($searchBox['iconClose'])->toContain('text-pg-primary-400');
    });
});

describe('Theme: Bootstrap5', function () {
    it('has a non-empty name property', function () {
        $theme = new Bootstrap5();

        expect($theme->name)
            ->toBeString()
            ->not->toBeEmpty()
            ->toBe('bootstrap5');
    });

    it('returns root path and contains base namespace', function () {
        $theme = new Bootstrap5();

        expect($theme->root())
            ->toContain('livewire-powergrid::components.frameworks')
            ->toBe('livewire-powergrid::components.frameworks.bootstrap5');
    });

    it('returns full table configuration', function () {
        $table = (new Bootstrap5())->table();

        expect($table)
            ->toHaveKeys(['layout', 'header', 'body'])
            ->and($table['layout'])->toHaveKeys(['base', 'div', 'table', 'container', 'actions'])
            ->and($table['header'])->toHaveKeys(['thead', 'tr', 'th', 'thAction'])
            ->and($table['body'])->toHaveKeys(['tbody', 'tr', 'td']);
    });

    it('returns column configuration', function () {
        expect((new Bootstrap5())->cols())
            ->toBeArray()
            ->toHaveKey('div');
    });

    it('returns footer configuration', function () {
        expect((new Bootstrap5())->footer())
            ->toBeArray()
            ->toHaveKeys(['view', 'select', 'footer']);
    });

    it('returns complete toggleable configuration with role', function () {
        $toggleable = (new Bootstrap5())->toggleable();

        expect($toggleable)
            ->toHaveKeys(['view', 'base', 'label', 'input', 'role'])
            ->and($toggleable['role'])->toBe('switch');
    });

    it('returns editable configuration', function () {
        expect((new Bootstrap5())->editable())
            ->toHaveKeys(['view', 'input']);
    });

    it('returns checkbox and radio configuration', function () {
        expect((new Bootstrap5())->checkbox())->toHaveKeys(['th', 'base', 'label', 'input'])
            ->and((new Bootstrap5())->radio())->toHaveKeys(['th', 'base', 'label', 'input']);
    });

    it('returns all filter configurations', function () {
        $theme = new Bootstrap5();

        expect($theme->filterBoolean())->toHaveKeys(['view', 'base', 'select'])
            ->and($theme->filterDatePicker())->toHaveKeys(['base', 'view', 'input'])
            ->and($theme->filterMultiSelect())->toHaveKeys(['view', 'base', 'select'])
            ->and($theme->filterNumber())->toHaveKeys(['view', 'input'])
            ->and($theme->filterSelect())->toHaveKeys(['view', 'base', 'select'])
            ->and($theme->filterInputText())->toHaveKeys(['view', 'base', 'select', 'input']);
    });

    it('returns searchbox with bootstrap icons', function () {
        $searchBox = (new Bootstrap5())->searchBox();

        expect($searchBox)
            ->toHaveKey('input')
            ->toHaveKey('iconSearch')
            ->and($searchBox['iconSearch'])->toBe('bi bi-search');
    });
});

describe('Theme: DaisyUI', function () {
    it('has a non-empty name property', function () {
        $theme = new DaisyUI();

        expect($theme->name)
            ->toBeString()
            ->not->toBeEmpty()
            ->toBe('daisyui');
    });

    it('returns root path and contains base namespace', function () {
        $theme = new DaisyUI();

        expect($theme->root())
            ->toContain('livewire-powergrid::components.frameworks')
            ->toBe('livewire-powergrid::components.frameworks.daisyui');
    });

    it('returns full table configuration with zebra style', function () {
        $table = (new DaisyUI())->table();

        expect($table['layout']['table'])->toContain('table-zebra');
    });

    it('returns layout configuration', function () {
        $layout = (new DaisyUI())->layout();

        expect($layout)
            ->toHaveKeys(['table', 'header', 'pagination', 'footer'])
            ->and($layout['table'])->toBe('livewire-powergrid::components.frameworks.daisyui.table-base');
    });

    it('toggleable contains only view', function () {
        expect((new DaisyUI())->toggleable())
            ->toHaveKey('view')
            ->toHaveCount(1);
    });

    it('uses DaisyUI component classes', function () {
        $theme = new DaisyUI();

        expect($theme->checkbox()['input'])->toBe('checkbox checkbox-sm')
            ->and($theme->radio()['input'])->toBe('radio')
            ->and($theme->editable()['input'])->toBe('input input-sm');
    });

    it('filters use select class', function () {
        $theme = new DaisyUI();

        expect($theme->filterBoolean()['select'])->toBe('select')
            ->and($theme->filterSelect()['select'])->toBe('select')
            ->and($theme->filterInputText()['select'])->toBe('select');
    });

    it('returns searchbox with DaisyUI icons', function () {
        $searchBox = (new DaisyUI())->searchBox();

        expect($searchBox)
            ->toHaveKey('input')
            ->toHaveKey('iconSearch')
            ->toHaveKey('iconClose')
            ->and($searchBox['iconSearch'])->toContain('text-base-content')
            ->and($searchBox['iconClose'])->toBe('text-base-content');
    });
});
