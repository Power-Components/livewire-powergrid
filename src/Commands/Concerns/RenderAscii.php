<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Concerns;

trait RenderAscii
{
    /**
     * PowerGrid name in Ascii Art
     */
    public function renderPowergridAscii(): void
    {
        $this->newLine();

        $this->line(<<<EOT

            <fg=yellow> __</>     <fg=green>____                          ______     _     __</>
            <fg=yellow>/ /_,</>  <fg=green>/ __ \____ _      _____  _____/ ____/____(_)___/ /</>
            <fg=yellow>/_ ,'</> <fg=green>/ /_/ / __ \ | /| / / _ \/ ___/ / __/ ___/ / __  / </>
            <fg=yellow>/'</>   <fg=green>/ ____/ /_/ / |/ |/ /  __/ /  / /_/ / /  / / /_/ /  </>
                <fg=green>/_/    \____/|__/|__/\___/_/   \____/_/  /_/\__,_/   </>  
        EOT);

        $this->newLine(2);
    }
}
