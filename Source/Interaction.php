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

    /** @var Authenticatable|Model */
    private $user;

    /** @var Dbt\Interactions\Log */
    private $log;

    public function __construct(InteractionModelInterface $model)
    {
        $this->model = $model;
        $this->properties = new Collection;
        $this->log = new Log;
    }

    /**
     * @inheritDoc
     */
    public function on(Model $model): InteractionInterface
    {
        $this->on = $model;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function by(Authenticatable $user): InteractionInterface
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function with(array $properties): InteractionInterface
    {
        foreach ($properties as $key => $value) {
            $this->properties->put($key, $value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function in(Log $log): InteractionInterface
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @inheritDoc
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
