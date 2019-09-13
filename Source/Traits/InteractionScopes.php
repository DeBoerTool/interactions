<?php

namespace Dbt\Interactions\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

trait InteractionScopes
{
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

    public function causerInScope()
    {
        return $this->hasMany($this->causer, 'id', 'causer_id');
    }
}
