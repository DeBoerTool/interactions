<?php

namespace Dbt\Interactions;

use Dbt\Interactions\Contracts\InteractionModelInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Activity
 * @property \Illuminate\Support\Collection properties
 * @property string description
 * @property string log_name
 * @package Dbt\Interaction
 * @method static Builder inLog($log)
 * @method static Builder causerIs($model)
 */
class Activity extends Model implements InteractionModelInterface
{
    use SoftDeletes;

    protected $table = 'interaction_log';

    // public function bind(string $connection, string $table)
    // {
    //     dd('Calling bind');
    //     $this->setConnection($connection);
    //     $this->setTable(config('interaction.table_name'));
    // }

    /**
     * Non-mass-assignable attributes
     * @var array
     */
    public $guarded = [];

    /**
     * Attributes to be converted to types/objects
     * @var array
     */
    protected $casts = [
        'properties' => 'collection',
    ];

    /**
     * The fully qualified model name
     */
    protected $causer = '';

    /*
     * Static Api ------------------------------------------------------------------------------------------------------
     */

    /**
     * @param Illuminate\Database\Eloquent\Model|null $user
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public static function logins(Model $user = null)
    {
        if ($user) {
            return static::query()
                ->where('causer_type', 'App\\User')
                ->where('causer_id', $user->id)
                ->get();
        }

        return null;
    }

    /*
     * Public Api ------------------------------------------------------------------------------------------------------
     */

    /**
     * @param $key
     * @return mixed
     */
    public function property(string $key)
    {
        return $this->properties->get($key);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function properties(): Collection
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function log(): string
    {
        return $this->log_name;
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /*
     * Public Setters --------------------------------------------------------------------------------------------------
     */

    /**
     * @param \Illuminate\Support\Collection $properties
     * @return \Dbt\Interfaces\Models\InteractionModelInterface
     */
    public function setProperties(Collection $properties): InteractionModelInterface
    {
        $this->setAttribute('properties', $properties);

        return $this;
    }

    /**
     * @param string $description
     * @return \Dbt\Interfaces\Models\InteractionModelInterface
     */
    public function setDescription(string $description): InteractionModelInterface
    {
        $this->setAttribute('description', $description);

        return $this;
    }

    /**
     * @param string $log
     * @return \Dbt\Interfaces\Models\InteractionModelInterface
     */
    public function setLog(string $log): InteractionModelInterface
    {
        $this->setAttribute('log_name', $log);

        return $this;
    }

    /**
     * Get a property by name
     * @param string $propertyName
     * @return mixed
     */
    public function getExtraProperty($propertyName)
    {
        return array_get($this->properties->toArray(), $propertyName);
    }

    /*
     * Scopes ----------------------------------------------------------------------------------------------------------
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
     * Only include entries with provided log names
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string | array  $logNames
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInLog(Builder $query, ...$logNames)
    {
        if (is_array($logNames[0])) {
            $logNames = $logNames[0];
        }

        return $query->whereIn('log_name', $logNames);
    }

    /**
     * Only include activities caused by $causer
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model $causer
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCausedBy(Builder $query, Model $causer)
    {
        return $query
            ->where('causer_type', get_class($causer))
            ->where('causer_id', $causer->getKey());
    }

    /**
     * Only include activities involving $subject
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model $subject
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSubject(Builder $query, Model $subject)
    {
        return $query
            ->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNameLike(Builder $query, $name)
    {
        return $query->where('log_name', 'like', '%' . $name . '%');
    }

    /**
     * Get the subset of results where the causer is a certain type of model.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $modelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCauserIs(Builder $query, $modelName)
    {
        $this->causer = $modelName;

        return $query->where('causer_type', $modelName);
    }

    /*
     * Relations -------------------------------------------------------------------------------------------------------
     */

    /**
     * Polymorphic relationship
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject(): MorphTo
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->morphTo();
    }

    /**
     * Polymorphic relationship
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function causer(): MorphTo
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function causerInScope()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->hasMany($this->causer, 'id', 'causer_id');
    }
}
