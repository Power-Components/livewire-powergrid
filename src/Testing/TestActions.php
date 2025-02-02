<?php

namespace PowerComponents\LivewirePowerGrid\Testing;

use Closure;

use PHPUnit\Framework\Assert;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\DataSourceBase;

class TestActions
{
    public function assertHasAction(): Closure
    {
        return function (string $action): static {
            $actionFound = collect(DataSourceBase::$actionsHtml)
                ->flatten(1)
                ->contains(fn (array $dishAction): bool => $dishAction['action'] === $action);

            Assert::assertTrue($actionFound, "Failed asserting that the action '$action' exists in the table.");

            return $this;
        };
    }

    public function assertActionHasIcon(): Closure
    {
        return function (string $action, string $icon, ?string $iconClass = null): static {
            /** @var array $actionFound */
            $actionFound = collect(DataSourceBase::$actionsHtml)
                ->flatten(1)
                ->first(function ($dishAction) use ($action, $icon) {
                    /** @var array $dishAction */
                    return $dishAction['action'] === $action && $dishAction['icon'] === $icon;
                });

            Assert::assertNotNull($actionFound, "Failed asserting that the action '$action' has the icon '$icon'.");

            if ($iconClass !== null) {
                $iconClassFound = isset($actionFound['iconAttributes']['class']) && str_contains($actionFound['iconAttributes']['class'], $iconClass);
                Assert::assertTrue($iconClassFound, "Failed asserting that the icon of action '$action' contains the class '$iconClass'.");
            }

            return $this;
        };
    }

    public function assertActionContainsAttribute(): Closure
    {
        return function (string $action, string $attribute, string $expected, array $expectedParams = []): static {
            $attributeFound = collect(DataSourceBase::$actionsHtml)
                ->flatten(1)
                ->first(function ($dishAction) use ($action, $attribute, $expected, $expectedParams) {
                    /** @var array $dishAction */
                    if ($dishAction['action'] === $action && isset($dishAction['attributes'][$attribute])) {
                        $attributeValue = $dishAction['attributes'][$attribute];

                        if (str_contains($attributeValue, 'JSON.parse')) {
                            preg_match("/JSON\.parse\('(.*)'\)/", $attributeValue, $matches);
                            $jsonEscaped = $matches[1] ?? null;

                            if ($jsonEscaped) {
                                $jsonStringClean = strval(json_decode('"' . $jsonEscaped . '"', true));
                                $data            = json_decode($jsonStringClean, true);

                                return $data == $expectedParams;
                            }
                        }

                        return str_contains($attributeValue, $expected);
                    }

                    return false;
                });

            Assert::assertNotNull($attributeFound, "Failed asserting that the '$attribute' of action '$action' contains the expected parameters.");

            return $this;
        };
    }
}
