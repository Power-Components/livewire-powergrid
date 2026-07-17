<?php

uses()->group('commands');

afterEach(function () {
    @unlink(base_path('.phpstorm.meta.php'));
});

it('generates the PhpStorm meta file with theme tokens and view aliases', function () {
    $path = base_path('.phpstorm.meta.php');
    @unlink($path);

    $this->artisan('powergrid:generate-theme-meta')
        ->assertExitCode(0);

    expect($path)->toBeFile();

    $content = file_get_contents($path);

    expect($content)
        ->toContain('namespace PHPSTORM_META')
        ->toContain('\theme()')
        ->toContain('\theme_view()')
        ->toContain('ThemeManager::theme()')
        ->toContain('ThemeManager::view()')
        // a canonical view alias is always present
        ->toContain("'header.enabled-filters'")
        ->toContain("'toggle-detail'");
});
