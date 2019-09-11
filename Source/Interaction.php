<?php

namespace Dbt\Interactions;

use Dbt\Interactions\Contracts\InteractionModelInterface;
use Dbt\Interactions\Contracts\InteractionInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Interaction implements InteractionInterface
{
    /** @var \Illuminate\Database\Eloquent\Model */
    private $model;

    /** @var \Illuminate\Support\Collection */
    private $properties;

    /** @var \Illuminate\Database\Eloquent\Model */
    private $on;

    /** @var \App\User */
    private $user;

    /** @var string */
    private $log = 'default';

    /**
     * Constructor.
     * @param \Dbt\Interfaces\Models\InteractionModelInterface $model
     */
    public function __construct(InteractionModelInterface $model)
    {
        $this->model = $model;
        $this->properties = new Collection;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Dbt\Interfaces\Services\InteractionInterface
     */
    public function on(Model $model): InteractionInterface
    {
        $this->on = $model;

        return $this;
    }

    /**
     * The user that performed the interaction, if any.
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return \Dbt\Interfaces\Services\InteractionInterface
     */
    public function by(Authenticatable $user): InteractionInterface
    {
        $this->user = $user;

        return $this;
    }

    /**
     * An array of properties to serialize to persist, if any.
     * @param array $properties
     * @return \Dbt\Interfaces\Services\InteractionInterface
     */
    public function with(array $properties): InteractionInterface
    {
        foreach ($properties as $key => $value) {
            $this->properties->put($key, $value);
        }

        return $this;
    }

    /**
     * The log name.
     * @param string $logName
     * @return \Dbt\Interfaces\Services\InteractionInterface
     */
    public function in(string $logName): InteractionInterface
    {
        $this->log = $logName;

        return $this;
    }

    /**
     * Persist the change with optional description.
     * @param string $description
     * @return mixed
     */
    public function save(string $description): InteractionModelInterface
    {
        if ($this->on) {
            $this->model->subject()->associate($this->on);
        }

        if ($this->user) {
            $this->model->causer()->associate($this->user);
        }

        if ($this->properties->count() > 0) {
            $this->model->setProperties($this->properties);
        }

        $this->model->setDescription($description)
            ->setLog($this->log);

        $this->model->save();

        return $this->model;
    }
}
