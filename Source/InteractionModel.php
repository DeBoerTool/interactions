<?php

namespace Dbt\Interactions;

use Dbt\Interactions\Contracts\InteractionModelInterface;
use Illuminate\Contracts\Auth\Authenticatable;
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
    public function log(): string
    {
        return $this->log_name;
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
    public function setLog(string $log): InteractionModelInterface
    {
        $this->setAttribute('log_name', $log);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtraProperty($propertyName)
    {
        return Arr::get($this->properties->toArray(), $propertyName);
    }

    /*
     * Scopes
     */

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

    /**
     * @param  array|string  ...$logNames
     */
    public function scopeInLog(Builder $query, ...$logNames): Builder
    {
        if (is_array($logNames[0])) {
            $logNames = $logNames[0];
        }

        return $query->whereIn('log_name', $logNames);
    }

    /**
     * @param Authenticatable|Model $causer
     */
    public function scopeCausedBy(Builder $query, Authenticatable $causer): Builder
    {
        return $query
            ->where('causer_type', get_class($causer))
            ->where('causer_id', $causer->getKey());
    }

    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->getKey());
    }

    public function scopeNameLike(Builder $query, $name)
    {
        return $query->where('log_name', 'like', '%' . $name . '%');
    }

    /**
     * TODO: Check if this actually works.
     */
    public function scopeCauserIs(Builder $query, $model)
    {
        $this->causer = $model;

        /**
         * TODO: Does this work for all polymorphic identities, eg, both FQCNs
         *
         */
        return $query->where('causer_type', $model);
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

    public function causerInScope()
    {
        return $this->hasMany($this->causer, 'id', 'causer_id');
    }
}
