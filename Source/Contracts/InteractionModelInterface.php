<?php

namespace Dbt\Interactions\Contracts;

use Dbt\Interactions\Log;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

interface InteractionModelInterface
{
    /**
     * The causer relationship
     */
    public function causer(): MorphTo;

    /**
     * The subject relationship.
     */
    public function subject(): MorphTo;

    /**
     * Get a property by name.
     * @return mixed
     */
    public function property(string $key);

    /**
     * Get all the properties.
     */
    public function properties(): Collection;

    /**
     * Get the log name.
     */
    public function log(): Log;

    /**
     * Get the description.
     */
    public function description(): string;

    /**
     * @return bool
     */
    public function save();

    /**
     * Set the properties.
     */
    public function setProperties(Collection $properties): self;

    /**
     * Set the description.
     */
    public function setDescription(string $description): self;

    /**
     * Set the log name.
     */
    public function setLog(Log $log): self;
}
