<?php

namespace Dbt\Interactions;

use Dbt\Interactions\Contracts\LogInterface;

class LogFactory
{

    /** @var array */
    protected $logs;

    public function __construct()
    {
        $this->logs = config('interaction.logs');
    }

    public function create($name) : LogInterface
    {
        foreach ($this->logs as $log) {
            $concrete = new $log;
            if ($name == $concrete->getName()) {
                return $concrete;
            }
        }
        throw new \Exception("Unable to create Log object from '{$name}'. Make sure you have Log class added in your config file.");
    }
}
