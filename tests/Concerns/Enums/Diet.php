<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Enums;

enum Diet: int
{
    case ALL = 0;
    case VEGAN = 1;
    case CELIAC = 2;

    public function labels(): string
    {
        return match ($this) {
            self::ALL => '🍽️ All diets',
            self::VEGAN => '🌱 Suitable for Vegans',
            self::CELIAC => '🥜 Suitable for Celiacs',
        };
    }

    /**
     * Sends labels to PowerGrid Enum Input
     */
    public function labelPowergridFilter(): string
    {
        return $this->labels();
    }
}
