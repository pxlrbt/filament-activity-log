<?php

namespace pxlrbt\FilamentActivityLog;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentActivityLogServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-activity-log';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasTranslations();
    }
}
