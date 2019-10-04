<?php

namespace Dbt\Interactions\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface InteractionInterface
{
    /**
     * The model the interaction was performed on, if any.
     */
    public function on(Model $model): self;

    /**
     * The user that performed the interaction, if any.
     */
    public function by(Authenticatable $user): self;

    /**
     * An array of properties to serialize to persist, if any.
     * @param array $properties
     */
    public function with(array $properties): self;

    /**
     * The log name.
     */
    public function in(LogInterface $log): self;

    /**
     * Persist the change with optional description.
     */
    public function save(string $description): InteractionModelInterface;
}
