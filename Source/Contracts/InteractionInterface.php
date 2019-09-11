<?php

namespace Dbt\Interactions\Contracts;

use Dbt\Interfaces\Models\InteractionModelInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface InteractionInterface
{
    /**
     * The model the interaction was performed on, if any.
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Dbt\Interfaces\Services\InteractionInterface
     */
    public function on(Model $model): InteractionInterface;

    /**
     * The user that performed the interaction, if any.
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return \Dbt\Interfaces\Services\InteractionInterface
     */
    public function by(Authenticatable $user): InteractionInterface;

    /**
     * An array of properties to serialize to persist, if any.
     * @param array $properties
     * @return \Dbt\Interfaces\Services\InteractionInterface
     */
    public function with(array $properties): InteractionInterface;

    /**
     * The log name.
     * @param string $logName
     * @return \Dbt\Interfaces\Services\InteractionInterface
     */
    public function in(string $logName): InteractionInterface;

    /**
     * Persist the change with optional description.
     * @param string $description
     * @return mixed
     */
    public function save(string $description): InteractionModelInterface;
}
