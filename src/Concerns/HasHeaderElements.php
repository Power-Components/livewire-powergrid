<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\Lang;
use PowerComponents\LivewirePowerGrid\Support\{HeaderElements, IconRenderer};

trait HasHeaderElements
{
    /** @var array<string, array<string, mixed>> */
    private array $headerElementCache = [];

    /**
     * @return array{key: string, token: string, icon: string, iconAttributes: array<string, mixed>, iconHtml: string, title: string, showLabel: bool, isComponentPath: bool, view: string}
     */
    public function headerElement(string $key): array
    {
        if (isset($this->headerElementCache[$key])) {
            /** @phpstan-ignore return.type */
            return $this->headerElementCache[$key];
        }

        $definition = HeaderElements::definition($key);
        $path = $definition['path'];

        $userIcon = $this->headerElementString($path.'.icon');
        $userIconAttributes = data_get($this->setUp, $path.'.iconAttributes', []);
        $userTitle = $this->headerElementString($path.'.title');
        $userShowLabel = data_get($this->setUp, $path.'.showLabel');
        $userView = $this->headerElementString($path.'.view');

        $icon = boolval(data_get($this->setUp, $path.'.iconDisabled', false))
            ? ''
            : ($userIcon !== '' ? $userIcon : theme($definition['icon_token'], $definition['icon']));

        $iconAttributes = is_array($userIconAttributes) && $userIconAttributes !== []
            ? $userIconAttributes
            : $this->headerElementIconAttributes($definition['class_token']);

        $resolved = [
            'key' => $key,
            'token' => $definition['token'],
            'icon' => $icon,
            'iconAttributes' => $iconAttributes,
            'iconHtml' => IconRenderer::render($icon, $iconAttributes),
            'title' => $this->headerElementTitle($userTitle, $definition['title']),
            'showLabel' => is_bool($userShowLabel) ? $userShowLabel : $definition['label'],
            'isComponentPath' => str_contains($icon, '::') || str_contains($icon, '.'),
            'view' => $userView !== '' ? $userView : theme($definition['token'].'.view', ''),
        ];

        return $this->headerElementCache[$key] = $resolved;
    }

    private function headerElementString(string $path): string
    {
        $value = data_get($this->setUp, $path);

        return is_string($value) ? $value : '';
    }

    /** @return array<string, mixed> */
    private function headerElementIconAttributes(string $classToken): array
    {
        $class = $classToken === '' ? '' : theme($classToken, '');

        return $class === '' ? [] : ['class' => $class];
    }

    private function headerElementTitle(string $userTitle, string $defaultTitleKey): string
    {
        if ($userTitle !== '') {
            return Lang::has($userTitle) ? (string) trans($userTitle) : $userTitle;
        }

        return $defaultTitleKey === '' ? '' : (string) trans($defaultTitleKey);
    }
}
