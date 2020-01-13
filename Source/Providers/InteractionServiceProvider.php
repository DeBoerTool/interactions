<?php

namespace Dbt\Interactions\Providers;

use Dbt\Interactions\Contracts\InteractionInterface;
use Dbt\Interactions\Interaction;
use Dbt\Interactions\InteractionModel;
use Illuminate\Support\ServiceProvider;

class InteractionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../Config/interaction.php', 'interaction');

        $concretion = function () {
            return new Interaction(new InteractionModel());
        };

        $this->app->bind(InteractionInterface::class, $concretion);
        $this->app->bind(config('interaction.binding'), $concretion);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../Config/interaction.php' => config_path('interaction.php'),
        ], 'dbt-interactions-config');

        $timestamp = date('Y_m_d_His', time());
        $this->publishes([
            __DIR__ . '/../../Migrations/create_interactions_table.php.stub' => database_path("/migrations/{$timestamp}_create_interactions_table.php"),
        ], 'dbt-interactions-migration');
    }
}
