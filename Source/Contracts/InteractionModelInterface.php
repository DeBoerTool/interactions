<?php

namespace Dbt\Interactions\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

interface InteractionModelInterface
{
    /**
     * The causer relationship
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function causer(): MorphTo;

    /**
     * The subject relationship.
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject(): MorphTo;

    /**
     * Get a property by name.
     * @param string $key
     * @return mixed
     */
    public function property(string $key);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function properties(): Collection;

    /**
     * @return string
     */
    public function log(): string;

    /**
     * @return string
     */
    public function description(): string;

    /**
     * @return bool
     */
    public function save();

    /**
     * Set the properties.
     * @param \Illuminate\Support\Collection $properties
     * @return mixed
     */
    public function setProperties(Collection $properties): InteractionModelInterface;

    /**
     * Set the description.
     * @param string $description
     * @return \Dbt\Interfaces\Models\InteractionModelInterface
     */
    public function setDescription(string $description): InteractionModelInterface;

    /**
     * Set the log name.
     * @param string $log
     * @return \Dbt\Interfaces\Models\InteractionModelInterface
     */
    public function setLog(string $log): InteractionModelInterface;
}
