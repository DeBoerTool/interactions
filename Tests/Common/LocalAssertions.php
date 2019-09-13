<?php

namespace Dbt\Interactions\Tests\Common;

/**
 * @mixin \Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase
 * @mixin \Dbt\Interactions\Tests\Common\IntegrationTestCase
 */
trait LocalAssertions
{
    protected function assertInteractionsTableHas ($attributes)
    {
        $this->assertDatabaseHas($this->interactionTable, $attributes);
    }
}
