<?php

namespace PowerComponents\LivewirePowerGrid\Support\Actions;

use PowerComponents\LivewirePowerGrid\Support\IconRenderer;
use PowerComponents\Turbine\Button;

final class ButtonRenderer
{
    /**
     * @param  array<int, array<string, mixed>>  $rules
     * @return array<string, mixed>
     *
     * @throws \Throwable
     */
    public static function resolve(Button $button, object $row, array $rules): array
    {
        $can = $button->can instanceof \Closure
            ? ($button->can)($row)
            : $button->can;

        if (! $can) {
            return ['hidden' => true, 'replaceHtml' => ''];
        }

        if (! empty($button->view)) {
            return [
                'hidden' => false,
                'replaceHtml' => view($button->view, ['action' => $button, 'row' => $row])->render(),
            ];
        }

        $attributes = $button->attributes;
        $slot = $button->slot;
        $replaceHtml = '';
        $hidden = false;

        foreach ($rules as $rule) {
            $forAction = $rule['action'] ?? null;

            $matches = is_array($forAction)
                ? in_array($button->action, $forAction, strict: true)
                : $forAction === $button->action;

            if (! ($rule['apply'] ?? false) || ! $matches) {
                continue;
            }

            if (data_get($rule, 'rule.hide')) {
                $hidden = true;
                break;
            }

            if (! empty($rule['replaceHtml'])) {
                $replaceHtml = $rule['replaceHtml'];
            }

            $setAttr = data_get($rule, 'rule.setAttribute');

            if (is_array($setAttr) && ! empty($setAttr)) {
                /** @var array<string, mixed>|array<int, array<string, mixed>> $setAttr */
                $entries = isset($setAttr['attribute']) ? [$setAttr] : $setAttr;

                foreach ($entries as $entry) {
                    if (is_array($entry) && isset($entry['attribute'], $entry['value'])) {
                        /** @var string $attributeKey */
                        $attributeKey = $entry['attribute'];
                        $attributes[$attributeKey] = $entry['value'];
                    }
                }
            }

            if ($slotOverride = data_get($rule, 'rule.slot')) {
                $slot = $slotOverride;
            }
        }

        if ($hidden) {
            return ['hidden' => true, 'replaceHtml' => ''];
        }

        return [
            'hidden' => false,
            'action' => $button->action,
            'tag' => $button->tag ?? 'button',
            'slot' => $slot,
            'iconHtml' => ! empty($button->icon) ? IconRenderer::render($button->icon, $button->iconAttributes) : '',
            'attributes' => $attributes,
            'replaceHtml' => $replaceHtml,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $actions
     */
    public static function html(array $actions): string
    {
        $html = '';

        foreach ($actions as $action) {
            $replaceHtml = $action['replaceHtml'] ?? '';

            if (is_string($replaceHtml) && $replaceHtml !== '') {
                $html .= $replaceHtml;

                continue;
            }

            $tag = $action['tag'] ?? 'button';
            $tag = is_string($tag) ? $tag : 'button';

            $attributePairs = [];
            $attributes = $action['attributes'] ?? [];
            if (is_array($attributes)) {
                foreach ($attributes as $key => $value) {
                    $attributePairs[] = $key.'="'.e(is_scalar($value) ? (string) $value : '').'"';
                }
            }

            $iconHtml = $action['iconHtml'] ?? '';
            $slot = $action['slot'] ?? '';

            $html .= '<'.$tag.' '.implode(' ', $attributePairs).'>'
                .(is_string($iconHtml) ? $iconHtml : '')
                .(is_string($slot) ? $slot : '')
                .'</'.$tag.'>';
        }

        return $html;
    }
}
