<?php

namespace Dbt\Interactions;

use Dbt\Interactions\Contracts\LogInterface;
use Exception;

class LogFactory
{
    /** @var string[] */
    protected array $logs;

    public function __construct ()
    {
        $this->logs = config('interaction.logs') ?? [];
    }

    /**
     * @throws \Exception
     */
    public function create (string $name): LogInterface
    {
        foreach ($this->logs as $log) {
            /** @var \Dbt\Interactions\Contracts\LogInterface $concrete */
            $concrete = new $log;

            if ($name === $concrete->getName()) {
                return $concrete;
            }
        }

        throw new Exception(sprintf(
            "Unable to create Log object from '%s'. Make sure you have Log class added in your config file.",
            $name
        ));
    }
}
