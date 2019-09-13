<?php

namespace Dbt\Interactions;

abstract class Log
{
    /** @var string */
    protected $name = 'default';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
