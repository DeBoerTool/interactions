<?php

namespace Dbt\Interactions\Tests\Support;

/**
 * @mixin \Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase
 * @mixin \Dbt\Interactions\Tests\Support\IntegrationTestCase
 */
trait LocalAssertions
{
    protected function assertInteractionsTableHas ($attributes)
    {
        $this->assertDatabaseHas($this->interactionTable, $attributes);
    }
}
