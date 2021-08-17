<?php

namespace Dbt\Interactions;

use Dbt\Interactions\Contracts\LogInterface;

abstract class AbstractLog implements LogInterface
{
    protected string $name = 'default';

    public function getName (): string
    {
        return $this->name;
    }
}
