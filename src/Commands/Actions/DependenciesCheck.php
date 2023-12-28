<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DependenciesCheck
{
    /**
     * Check if flatpickr is installed
     *
     */
    public static function flatpickr(): ?string
    {
        $filesToCheck = [
            base_path('tailwind.config.js'),
            base_path('resources/js/app.js'),
        ];

        $message = "\n💡 It seems you are not using the <comment>flatpickr</comment> plugin.\n   Please check: <comment>https://livewire-powergrid.com/table/column-filters.html#filter-datetimepicker</comment> for more information.";

        foreach ($filesToCheck as $file) {
            if (File::exists($file) && !Str::contains(File::get($file), "flatpickr")) {
                return $message;
            }
        }

        return null;
    }

    /**
     * Check if openspout/openspout is installed
     *
     */
    public static function openspout(): ?string
    {
        $file = base_path() . '/' . 'composer.json';

        if (File::exists($file)) {
            $content = File::get($file);

            if (!Str::contains($content, "openspout/openspout")) {
                return("\n💡 It seems you are using the <comment>openspout/openspout</comment> package.\n   Please check: <comment>https://livewire-powergrid.com/table/features-setup.html#exportable</comment> for more information.");
            }
        }

        return null;
    }
}
