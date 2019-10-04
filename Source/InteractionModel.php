<?php

namespace Dbt\Interactions;

use Dbt\Interactions\Contracts\InteractionModelInterface;
use Dbt\Interactions\Contracts\LogInterface;
use Dbt\Interactions\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class Activity
 * @property \Illuminate\Support\Collection properties
 * @property string description
 * @property string log_name
 * @package Dbt\Interactions
 * @method static Builder inLog($log)
 * @method static Builder causerIs($model)
 */
class InteractionModel extends Model implements InteractionModelInterface
{
    use SoftDeletes;

    /** @var array */
    public $guarded = [];

    /** @var array */
    protected $casts = ['properties' => 'collection',];

    /** @var string */
    protected $causer = '';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('interaction.table_name');
    }

    /*
     * Interface Api
     */

    /**
     * @inheritDoc
     */
    public function property(string $key)
    {
        return $this->properties->get($key);
    }

    /**
     * @inheritDoc
     */
    public function properties(): Collection
    {
        return $this->properties;
    }

    /**
     * @inheritDoc
     */
    public function log(): LogInterface
    {
        return (new LogFactory())->create($this->log_name);
    }

    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setProperties(Collection $properties): InteractionModelInterface
    {
        $this->setAttribute('properties', $properties);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description): InteractionModelInterface
    {
        $this->setAttribute('description', $description);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLog(LogInterface $log): InteractionModelInterface
    {
        $this->setAttribute('log_name', $log->getName());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtraProperty($propertyName)
    {
        return Arr::get($this->properties->toArray(), $propertyName);
    }

    /**
     * Get the changes attribute, used in model event context
     * @return \Illuminate\Support\Collection
     */
    public function getChangesAttribute()
    {
        return collect(array_filter($this->properties->toArray(), function ($key) {
            return in_array($key, ['attributes', 'old']);
        }, ARRAY_FILTER_USE_KEY));
    }

    /*
     * Relations
     */

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
