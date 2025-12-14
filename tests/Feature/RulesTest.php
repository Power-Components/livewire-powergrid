<?php

use PowerComponents\LivewirePowerGrid\Components\Rules\{RuleActions, RuleCheckbox, RuleEditOnClick, RuleManager, RuleRadio, RuleRows, RuleToggleable};
use PowerComponents\LivewirePowerGrid\Facades\Rule;

describe('RuleManager', function () {
    it('should have correct type constants', function () {
        expect(RuleManager::TYPE_ACTIONS)->toBe('actions')
            ->and(RuleManager::TYPE_ROWS)->toBe('pg:rows')
            ->and(RuleManager::TYPE_TOGGLEABLE)->toBe('pg:toggleable')
            ->and(RuleManager::TYPE_EDIT_ON_CLICK)->toBe('pg:editOnClick')
            ->and(RuleManager::TYPE_CHECKBOX)->toBe('pg:checkbox')
            ->and(RuleManager::TYPE_RADIO)->toBe('pg:radio')
            ->and(RuleManager::TYPE_COLUMN)->toBe('pg:column');
    });

    it('should return applicable modifiers', function () {
        $modifiers = RuleManager::applicableModifiers();

        expect($modifiers)->toBeArray()
            ->and($modifiers)->toContain('bladeComponent', 'detailView', 'disable', 'dispatch', 'hide', 'slot', 'setAttribute');
    });

    it('should create RuleActions instance', function () {
        $manager = new RuleManager();
        $rule = $manager->button('test-button');

        expect($rule)->toBeInstanceOf(RuleActions::class)
            ->and($rule->forAction)->toBe('test-button');
    });

    it('should create RuleRows instance', function () {
        $manager = new RuleManager();
        $rule = $manager->rows();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create RuleToggleable instance', function () {
        $manager = new RuleManager();
        $rule = $manager->toggleable('active');

        expect($rule)->toBeInstanceOf(RuleToggleable::class)
            ->and($rule->column)->toBe('active')
            ->and($rule->forAction)->toBe('active');
    });

    it('should create RuleEditOnClick instance', function () {
        $manager = new RuleManager();
        $rule = $manager->editOnClick('name');

        expect($rule)->toBeInstanceOf(RuleEditOnClick::class)
            ->and($rule->column)->toBe('name')
            ->and($rule->forAction)->toBe('name');
    });

    it('should create RuleCheckbox instance', function () {
        $manager = new RuleManager();
        $rule = $manager->checkbox();

        expect($rule)->toBeInstanceOf(RuleCheckbox::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_CHECKBOX);
    });

    it('should create RuleRadio instance', function () {
        $manager = new RuleManager();
        $rule = $manager->radio();

        expect($rule)->toBeInstanceOf(RuleRadio::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_RADIO);
    });
});

describe('RuleActions', function () {
    it('should create button rule with hide modifier', function () {
        $rule = Rule::button('edit')->hide();

        expect($rule)->toBeInstanceOf(RuleActions::class)
            ->and($rule->forAction)->toBe('edit');
    });

    it('should create button rule with slot modifier', function () {
        $rule = Rule::button('edit')->slot('Edit Item');

        expect($rule)->toBeInstanceOf(RuleActions::class)
            ->and($rule->forAction)->toBe('edit');
    });

    it('should create button rule with setAttribute modifier', function () {
        $rule = Rule::button('edit')->setAttribute('class', 'btn-primary');

        expect($rule)->toBeInstanceOf(RuleActions::class)
            ->and($rule->forAction)->toBe('edit');
    });

    it('should create button rule with bladeComponent modifier', function () {
        $rule = Rule::button('edit')->bladeComponent('components.custom-button', ['id' => 1]);

        expect($rule)->toBeInstanceOf(RuleActions::class)
            ->and($rule->forAction)->toBe('edit');
    });

    it('should chain multiple modifiers', function () {
        $rule = Rule::button('edit')
            ->slot('Edit')
            ->setAttribute('class', 'btn-primary')
            ->when(fn ($row) => $row->id === 1);

        expect($rule)->toBeInstanceOf(RuleActions::class)
            ->and($rule->forAction)->toBe('edit');
    });
});

describe('RuleRows', function () {
    it('should create rows rule with showToggleable', function () {
        $rule = Rule::rows()->showToggleable();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with hideToggleable', function () {
        $rule = Rule::rows()->hideToggleable();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with enableEditOnClick', function () {
        $rule = Rule::rows()->enableEditOnClick();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with disableEditOnClick', function () {
        $rule = Rule::rows()->disableEditOnClick();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with showToggleDetail', function () {
        $rule = Rule::rows()->showToggleDetail();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with hideToggleDetail', function () {
        $rule = Rule::rows()->hideToggleDetail();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with detailView', function () {
        $rule = Rule::rows()->detailView('components.detail', ['name' => 'Test']);

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with setAttribute', function () {
        $rule = Rule::rows()->setAttribute('data-id', '123');

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with loop condition', function () {
        $rule = Rule::rows()->loop(fn ($loop) => $loop->first);

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with firstOnPage', function () {
        $rule = Rule::rows()->firstOnPage();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with lastOnPage', function () {
        $rule = Rule::rows()->lastOnPage();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should create rows rule with alternating', function () {
        $rule = Rule::rows()->alternating();

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });

    it('should chain multiple row modifiers', function () {
        $rule = Rule::rows()
            ->hideToggleable()
            ->disableEditOnClick()
            ->hideToggleDetail()
            ->when(fn ($row) => $row->id === 5);

        expect($rule)->toBeInstanceOf(RuleRows::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_ROWS);
    });
});

describe('RuleToggleable', function () {
    it('should create toggleable rule with hide', function () {
        $rule = Rule::toggleable('active')->hide();

        expect($rule)->toBeInstanceOf(RuleToggleable::class)
            ->and($rule->column)->toBe('active')
            ->and($rule->forAction)->toBe('active');
    });

    it('should create toggleable rule with show', function () {
        $rule = Rule::toggleable('active')->show();

        expect($rule)->toBeInstanceOf(RuleToggleable::class)
            ->and($rule->column)->toBe('active')
            ->and($rule->forAction)->toBe('active');
    });

    it('should create toggleable rule with correct column name', function () {
        $rule = Rule::toggleable('in_stock');

        expect($rule->column)->toBe('in_stock')
            ->and($rule->forAction)->toBe('in_stock');
    });
});

describe('RuleEditOnClick', function () {
    it('should create editOnClick rule with disable', function () {
        $rule = Rule::editOnClick('name')->disable();

        expect($rule)->toBeInstanceOf(RuleEditOnClick::class)
            ->and($rule->column)->toBe('name')
            ->and($rule->forAction)->toBe('name');
    });

    it('should create editOnClick rule with enable', function () {
        $rule = Rule::editOnClick('name')->enable();

        expect($rule)->toBeInstanceOf(RuleEditOnClick::class)
            ->and($rule->column)->toBe('name')
            ->and($rule->forAction)->toBe('name');
    });

    it('should create editOnClick rule with correct column name', function () {
        $rule = Rule::editOnClick('serving_at');

        expect($rule->column)->toBe('serving_at')
            ->and($rule->forAction)->toBe('serving_at');
    });
});

describe('RuleCheckbox', function () {
    it('should create checkbox rule with hide', function () {
        $rule = Rule::checkbox()->hide();

        expect($rule)->toBeInstanceOf(RuleCheckbox::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_CHECKBOX);
    });

    it('should create checkbox rule with disable', function () {
        $rule = Rule::checkbox()->disable();

        expect($rule)->toBeInstanceOf(RuleCheckbox::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_CHECKBOX);
    });

    it('should create checkbox rule with setAttribute', function () {
        $rule = Rule::checkbox()->setAttribute('data-test', 'value');

        expect($rule)->toBeInstanceOf(RuleCheckbox::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_CHECKBOX);
    });

    it('should create checkbox rule with applyRowClasses', function () {
        $rule = Rule::checkbox()->applyRowClasses('bg-red-500');

        expect($rule)->toBeInstanceOf(RuleCheckbox::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_CHECKBOX);
    });

    it('should chain checkbox modifiers', function () {
        $rule = Rule::checkbox()
            ->hide()
            ->disable()
            ->applyRowClasses('disabled')
            ->when(fn ($row) => $row->active === false);

        expect($rule)->toBeInstanceOf(RuleCheckbox::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_CHECKBOX);
    });
});

describe('RuleRadio', function () {
    it('should create radio rule with hide', function () {
        $rule = Rule::radio()->hide();

        expect($rule)->toBeInstanceOf(RuleRadio::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_RADIO);
    });

    it('should create radio rule with disable', function () {
        $rule = Rule::radio()->disable();

        expect($rule)->toBeInstanceOf(RuleRadio::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_RADIO);
    });

    it('should create radio rule with setAttribute', function () {
        $rule = Rule::radio()->setAttribute('data-test', 'value');

        expect($rule)->toBeInstanceOf(RuleRadio::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_RADIO);
    });

    it('should create radio rule with applyRowClasses', function () {
        $rule = Rule::radio()->applyRowClasses('bg-blue-500');

        expect($rule)->toBeInstanceOf(RuleRadio::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_RADIO);
    });

    it('should chain radio modifiers', function () {
        $rule = Rule::radio()
            ->hide()
            ->disable()
            ->applyRowClasses('selected')
            ->when(fn ($row) => $row->selected === true);

        expect($rule)->toBeInstanceOf(RuleRadio::class)
            ->and($rule->forAction)->toBe(RuleManager::TYPE_RADIO);
    });
});

describe('Rule Facade', function () {
    it('should create rules through facade', function () {
        expect(Rule::button('test'))->toBeInstanceOf(RuleActions::class)
            ->and(Rule::rows())->toBeInstanceOf(RuleRows::class)
            ->and(Rule::toggleable('field'))->toBeInstanceOf(RuleToggleable::class)
            ->and(Rule::editOnClick('field'))->toBeInstanceOf(RuleEditOnClick::class)
            ->and(Rule::checkbox())->toBeInstanceOf(RuleCheckbox::class)
            ->and(Rule::radio())->toBeInstanceOf(RuleRadio::class);
    });

    it('should create button rules with different names', function () {
        $editRule = Rule::button('edit');
        $deleteRule = Rule::button('delete');
        $dispatchRule = Rule::button('dispatch');

        expect($editRule->forAction)->toBe('edit')
            ->and($deleteRule->forAction)->toBe('delete')
            ->and($dispatchRule->forAction)->toBe('dispatch');
    });
});

describe('RuleActions with when condition', function () {
    it('should apply hide with when condition', function () {
        $rule = Rule::button('edit')
            ->hide()
            ->when(fn ($row) => $row->id > 10);

        expect($rule)->toBeInstanceOf(RuleActions::class)
            ->and($rule->forAction)->toBe('edit');
    });

    it('should apply slot with when condition', function () {
        $rule = Rule::button('edit')
            ->slot('Custom Text')
            ->when(fn ($row) => $row->status === 'active');

        expect($rule)->toBeInstanceOf(RuleActions::class)
            ->and($rule->forAction)->toBe('edit');
    });

    it('should apply disable with when condition', function () {
        $rule = Rule::button('delete')
            ->disable()
            ->when(fn ($row) => $row->protected === true);

        expect($rule)->toBeInstanceOf(RuleActions::class)
            ->and($rule->forAction)->toBe('delete');
    });
});

describe('RuleRows with setAttribute variations', function () {
    it('should set multiple attributes', function () {
        $rule = Rule::rows()
            ->setAttribute('class', 'bg-red-500')
            ->setAttribute('data-id', '123')
            ->when(fn ($row) => $row->error === true);

        expect($rule)->toBeInstanceOf(RuleRows::class);
    });

    it('should set attribute with null values', function () {
        $rule = Rule::rows()->setAttribute(null, null);

        expect($rule)->toBeInstanceOf(RuleRows::class);
    });
});

describe('RuleCheckbox with conditions', function () {
    it('should combine hide and disable', function () {
        $rule = Rule::checkbox()
            ->hide()
            ->disable();

        expect($rule)->toBeInstanceOf(RuleCheckbox::class);
    });

    it('should apply row classes with when condition', function () {
        $rule = Rule::checkbox()
            ->applyRowClasses('selected')
            ->when(fn ($row) => $row->selected === true);

        expect($rule)->toBeInstanceOf(RuleCheckbox::class);
    });
});

describe('RuleRadio with conditions', function () {
    it('should combine hide and disable', function () {
        $rule = Rule::radio()
            ->hide()
            ->disable();

        expect($rule)->toBeInstanceOf(RuleRadio::class);
    });

    it('should apply row classes with when condition', function () {
        $rule = Rule::radio()
            ->applyRowClasses('highlighted')
            ->when(fn ($row) => $row->highlighted === true);

        expect($rule)->toBeInstanceOf(RuleRadio::class);
    });
});

describe('Complex rule scenarios', function () {
    it('should create complex button rule with multiple conditions', function () {
        $rule = Rule::button('edit')
            ->slot('Edit')
            ->setAttribute('class', 'btn-primary')
            ->setAttribute('data-toggle', 'modal')
            ->when(fn ($row) => $row->editable === true);

        expect($rule)->toBeInstanceOf(RuleActions::class);
    });

    it('should create complex rows rule with all visibility toggles', function () {
        $rule = Rule::rows()
            ->hideToggleable()
            ->disableEditOnClick()
            ->hideToggleDetail()
            ->setAttribute('class', 'disabled-row')
            ->when(fn ($row) => $row->archived === true);

        expect($rule)->toBeInstanceOf(RuleRows::class);
    });

    it('should create rows rule with detail view and conditions', function () {
        $rule = Rule::rows()
            ->detailView('components.row-detail', ['expanded' => true])
            ->showToggleDetail()
            ->when(fn ($row) => $row->hasDetails === true);

        expect($rule)->toBeInstanceOf(RuleRows::class);
    });
});
