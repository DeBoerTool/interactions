<?php

namespace Dbt\Interactions\Tests\Integration;

use Dbt\Interactions\Contracts\InteractionInterface;
use Dbt\Interactions\Interaction;
use Dbt\Interactions\Tests\Support\IntegrationTestCase;

class ResolutionTest extends IntegrationTestCase
{
    /** @test */
    public function resolving_from_the_container (): void
    {
        $concretion = resolve(InteractionInterface::class);

        $this->assertInstanceOf(Interaction::class, $concretion);

        $concretion = resolve('interaction');

        $this->assertInstanceOf(Interaction::class, $concretion);
    }
}
