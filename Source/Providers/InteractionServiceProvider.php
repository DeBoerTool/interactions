<?php

namespace Dbt\Interactions\Providers;

use Illuminate\Support\ServiceProvider;

class InteractionServiceProvider extends ServiceProvider
{
    public function register()
    { }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../Config/interaction.php' => config_path('interaction.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../Config/interaction.php', 'interaction');
    }
}
