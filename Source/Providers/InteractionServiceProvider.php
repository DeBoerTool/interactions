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
            __DIR__ . '/../../Config/interaction.php' => config_path('interaction.php'),
        ], 'dbt-interaction-config');
        $this->mergeConfigFrom(__DIR__ . '/../../Config/interaction.php', 'interaction');


        $timestamp = date('Y_m_d_His', time());
        $this->publishes([
            __DIR__ . '/../../Migrations/create_interactions_table.php.stub' => database_path("/migrations/{$timestamp}_create_interactions_table.php"),
        ], 'dbt-interaction-migration');
    }
}
