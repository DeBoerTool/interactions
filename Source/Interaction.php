<?php

namespace Dbt\Interactions;

use Dbt\Interactions\Contracts\InteractionModelInterface;
use Dbt\Interactions\Contracts\InteractionInterface;
use Dbt\Interactions\Contracts\LogInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Interaction implements InteractionInterface
{
    private InteractionModelInterface $model;
    private Collection $properties;
    private LogInterface $log;
    private ?Model $on;

    /** @var \Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model|null  */
    private ?Authenticatable $user;

    public function __construct (InteractionModelInterface $model)
    {
        $this->model = $model;
        $this->properties = new Collection();
        $this->log = new Log();

        $this->on = null;
        $this->user = null;
    }

    /**
     * @inheritDoc
     */
    public function on (Model $model): self
    {
        $this->on = $model;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function by (Authenticatable $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function with (array $properties): self
    {
        foreach ($properties as $key => $value) {
            $this->properties->put($key, $value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function in (LogInterface $log): self
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save (string $description): InteractionModelInterface
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

        $this->model->setDescription($description)->setLog($this->log);

        return tap($this->model)->save();
    }
}
