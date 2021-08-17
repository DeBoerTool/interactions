<?php

namespace Dbt\Interactions\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait InteractionScopes
{
    /**
     * @param Authenticatable|Model $causer
     */
    public function scopeCausedBy (Builder $query, Authenticatable $causer): Builder
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

    /**
     * TODO: Check if this actually works.
     */
    public function scopeCauserIs (Builder $query,  $model)
    {
        /**
         * TODO: Does this work for all polymorphic identities, eg, both FQCNs
         *
         */
        return $query->where('causer_type', $model);
    }

    /**
     * @param  array|string  ...$logs
     */
    public function scopeInLog(Builder $query, ...$logs): Builder
    {
        if (is_array($logs[0])) {
            $logs = $logs[0];
        }

        $logNames = array_map(function ($log) {
            return $log->getName();
        }, $logs);

        return $query->whereIn('log_name', $logNames);
    }

    public function causerInScope()
    {
        return $this->hasMany($this->causer, 'id', 'causer_id');
    }
}
