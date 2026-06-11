<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Concerns;

/** @codeCoverageIgnore */
trait RenderAscii
{
    /**
     * PowerGrid name in ASCII Art with gradient colors.
     */
    public function renderPowergridAscii(): void
    {
        $this->newLine();

        $lines = [
            ' ██████╗  ██████╗ ██╗    ██╗███████╗██████╗  ██████╗ ██████╗ ██╗██████╗ ',
            ' ██╔══██╗██╔═══██╗██║    ██║██╔════╝██╔══██╗██╔════╝ ██╔══██╗██║██╔══██╗',
            ' ██████╔╝██║   ██║██║ █╗ ██║█████╗  ██████╔╝██║  ███╗██████╔╝██║██║  ██║',
            ' ██╔═══╝ ██║   ██║██║███╗██║██╔══╝  ██╔══██╗██║   ██║██╔══██╗██║██║  ██║',
            ' ██║     ╚██████╔╝╚███╔███╔╝███████╗██║  ██║╚██████╔╝██║  ██║██║██████╔╝',
            ' ╚═╝      ╚═════╝  ╚══╝╚══╝ ╚══════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝  ╚═╝╚═╝╚═════╝',
        ];

        $gradients = [
            [213, 177, 141, 105, 69, 39],   // Vaporwave
            [81, 75, 69, 63, 57, 21],       // Ocean
            [214, 208, 202, 196, 160, 124], // Sunset
            [51, 50, 49, 48, 47, 41],       // Aurora
            [227, 221, 215, 209, 203, 197], // Ember
            [201, 165, 129, 93, 57, 21],    // Cyberpunk
            [196, 160, 124, 88, 52, 88],    // Red
            [46, 47, 48, 49, 50, 51],       // Matrix
        ];

        $gradient = $gradients[array_rand($gradients)];

        foreach ($lines as $index => $line) {
            $color = $gradient[$index];
            $this->getOutput()->writeln("\e[38;5;{$color}m{$line}\e[0m");
        }

        $lastColor = end($gradient);
        $this->getOutput()->writeln("\e[38;5;{$lastColor}m                        ⚡ P O W E R G R I D\e[0m");

        $this->newLine();
    }
}
