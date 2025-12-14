<?php

use PowerComponents\LivewirePowerGrid\Components\Rules\Support\{DisableRule, HideRule, SlotRule};

describe('DisableRule', function () {
    it('should return disabled attribute when rule data is true', function () {
        $rule = new DisableRule();
        $result = $rule->apply(true);

        expect($result)->toBeArray()
            ->and($result)->toHaveKey('attributes')
            ->and($result['attributes'])->toBe(['disabled' => 'disabled']);
    });

    it('should return empty array when rule data is false', function () {
        $rule = new DisableRule();
        $result = $rule->apply(false);

        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });

    it('should return empty array when no parameter is passed', function () {
        $rule = new DisableRule();
        $result = $rule->apply();

        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });
});

describe('HideRule', function () {
    it('should return hide true when rule data is true', function () {
        $rule = new HideRule();
        $result = $rule->apply(true);

        expect($result)->toBeArray()
            ->and($result)->toHaveKey('hide')
            ->and($result['hide'])->toBeTrue();
    });

    it('should return empty array when rule data is false', function () {
        $rule = new HideRule();
        $result = $rule->apply(false);

        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });

    it('should return empty array when no parameter is passed', function () {
        $rule = new HideRule();
        $result = $rule->apply();

        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });
});

describe('SlotRule', function () {
    it('should return slot with provided string', function () {
        $rule = new SlotRule();
        $result = $rule->apply('Edit Item');

        expect($result)->toBeArray()
            ->and($result)->toHaveKey('slot')
            ->and($result['slot'])->toBe('Edit Item');
    });

    it('should handle empty string', function () {
        $rule = new SlotRule();
        $result = $rule->apply('');

        expect($result)->toBeArray()
            ->and($result)->toHaveKey('slot')
            ->and($result['slot'])->toBe('');
    });

    it('should handle HTML content in slot', function () {
        $rule = new SlotRule();
        $result = $rule->apply('<i class="fas fa-edit"></i> Edit');

        expect($result)->toBeArray()
            ->and($result)->toHaveKey('slot')
            ->and($result['slot'])->toBe('<i class="fas fa-edit"></i> Edit');
    });

    it('should handle special characters in slot', function () {
        $rule = new SlotRule();
        $result = $rule->apply('Editar & Salvar');

        expect($result)->toBeArray()
            ->and($result)->toHaveKey('slot')
            ->and($result['slot'])->toBe('Editar & Salvar');
    });
});
