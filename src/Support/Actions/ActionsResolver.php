<?php

namespace PowerComponents\LivewirePowerGrid\Support\Actions;

use Closure;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Components\Rules\BaseRule;
use PowerComponents\LivewirePowerGrid\Contracts\PowerGridContext;

final readonly class ActionsResolver
{
    public function __construct(private PowerGridContext $context) {}

    /** @return list<array<string, mixed>> */
    public function forRow(object $row): array
    {
        if (! method_exists($this->context, 'actions')) {
            return [];
        }

        /** @var list<Button> $buttons */
        $buttons = (array) $this->context->actions($row);

        /** @var list<BaseRule> $rules */
        $rules = method_exists($this->context, 'actionRules')
            ? array_filter((array) $this->context->actionRules($row), fn ($rule) => $rule instanceof BaseRule)
            : [];

        $descriptors = [];

        foreach ($buttons as $button) {
            if (! $button instanceof Button) {
                continue;
            }

            $descriptors[] = $this->describe($button, $row, $rules);
        }

        return $descriptors;
    }

    /**
     * @param  list<BaseRule>  $rules
     * @return array<string, mixed>
     */
    private function describe(Button $button, object $row, array $rules): array
    {
        $can = $button->can;
        $visible = $can instanceof Closure ? (bool) $can($row) : (bool) $can;

        $attributes = $button->attributes;
        $label = $button->slot;

        foreach ($rules as $rule) {
            if ($rule->forAction !== $button->action || ! $this->conditionPasses($rule, $row)) {
                continue;
            }

            if (data_get($rule->rule, 'hide')) {
                $visible = false;
            }

            $slot = data_get($rule->rule, 'slot');

            if (is_string($slot)) {
                $label = $slot;
            }

            foreach ((array) data_get($rule->rule, 'setAttribute', []) as $set) {
                $attribute = data_get($set, 'attribute');

                if (is_string($attribute)) {
                    $attributes[$attribute] = data_get($set, 'value');
                }
            }
        }

        return [
            'id' => $button->action,
            'label' => $label,
            'icon' => $button->icon ?: null,
            'tag' => $button->tag,
            'visible' => $visible,
            'disabled' => isset($attributes['disabled']),
            'confirm' => $this->confirm($attributes),
            'event' => $button->eventMeta ?: null,
            'attributes' => $this->publicAttributes($attributes),
        ];
    }

    private function conditionPasses(BaseRule $rule, object $row): bool
    {
        $when = data_get($rule->rule, 'when');

        if ($when instanceof Closure) {
            return (bool) $when($row);
        }

        $unless = data_get($rule->rule, 'unless');

        if ($unless instanceof Closure) {
            return ! (bool) $unless($row);
        }

        return false;
    }

    /** @param  array<string, mixed>  $attributes */
    private function confirm(array $attributes): ?string
    {
        $confirm = $attributes['wire:confirm'] ?? $attributes['wire:confirm.prompt'] ?? null;

        return is_string($confirm) ? $confirm : null;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function publicAttributes(array $attributes): array
    {
        $stripped = [];

        foreach ($attributes as $key => $value) {
            $key = (string) $key;

            if (in_array($key, ['href', 'target', 'disabled'], true) || str_starts_with($key, 'wire:')) {
                continue;
            }

            $stripped[$key] = $value;
        }

        return $stripped;
    }
}
