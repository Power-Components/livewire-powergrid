<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TailwindForm
{
    /**
     * Check if Tailwindform is installed
     *
     * @return string|null
     */
    public static function check()
    {
        $tailwindConfigFile = base_path() . '/' . 'tailwind.config.js';
      
        if (File::exists($tailwindConfigFile)) {
            $fileContent = File::get($tailwindConfigFile);
  
            if (Str::contains($fileContent, "require('@tailwindcss/forms')") === true) {
                return("\n💡 It seems you are using the plugin <comment>Tailwindcss/form</comment>.\n   Please check: <comment>https://livewire-powergrid.com/#/get-started/configure?id=_43-tailwind-forms</comment> for more information.");
            }
        }
        
        return null;
    }
}
