<?php

namespace Dbt\Interactions\Providers;

use Illuminate\Support\ServiceProvider;

class MigrationsProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__) . '/../Migrations');
    }
}
