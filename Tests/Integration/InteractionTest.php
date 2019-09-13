<?php

namespace Dbt\Interactions\Tests\Integration;

use Dbt\Interactions\Contracts\InteractionInterface;
use Dbt\Interactions\Log;
use Dbt\Interactions\Tests\Common\Fixtures\Logs\InventoryUpdateLog;
use Dbt\Interactions\Tests\Common\IntegrationTestCase;

class InteractionTest extends IntegrationTestCase
{
    /** @test */
    public function create_a_log_entry()
    {
        $this->app->make(InteractionInterface::class)->save('Test Interaction');

        $this->assertInteractionsTableHas([
            'description' => 'Test Interaction'
        ]);
    }

    /** @test */
    public function log_can_have_a_causer()
    {
        $this->app->make(InteractionInterface::class)->by($this->testUser)->save('Log with test user');

        $this->assertInteractionsTableHas([
            'description' => 'Log with test user',
            'causer_id' => $this->testUser->id,
            'causer_type' => get_class($this->testUser)
        ]);
    }

    /** @test */
    public function log_can_have_a_subject()
    {
        $this->app->make(InteractionInterface::class)
            ->by($this->testUser)
            ->on($this->testPost)
            ->save('Log by test user for test post with description');

        $this->assertInteractionsTableHas([
            'description' => 'Log by test user for test post with description',
            'causer_id' => $this->testUser->id,
            'causer_type' => get_class($this->testUser),
            'subject_id' => $this->testPost->id,
            'subject_type' => get_class($this->testPost),
        ]);
    }

    /** @test */
    public function log_can_have_properties()
    {
        $this->app->make(InteractionInterface::class)->with(['name' => 'Jane Doe'])->save('Log with properties');

        $interaction = \DB::table($this->interactionTable)->first();
        $this->assertEquals(json_encode(['name' => 'Jane Doe']), $interaction->properties);
        $this->assertEquals('Log with properties', $interaction->description);
    }

    /** @test */
    public function log_can_have_a_name()
    {
        $inventoryUpdateLog = new InventoryUpdateLog;
        $this->app->make(InteractionInterface::class)->in($inventoryUpdateLog)->save('Log with name');

        $this->assertInteractionsTableHas([
            'description' => 'Log with name',
            'log_name' => $inventoryUpdateLog->getName(),
        ]);
    }
}
