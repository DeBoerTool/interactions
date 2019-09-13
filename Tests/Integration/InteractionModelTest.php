<?php

namespace Dbt\Interactions\Tests\Integration;

use Dbt\Interactions\Interaction;
use Dbt\Interactions\Tests\Common\Fixtures\User;
use Dbt\Interactions\Contracts\InteractionInterface;
use Dbt\Interactions\Tests\Common\IntegrationTestCase;
use Dbt\Interactions\Tests\Common\Fixtures\Interaction as InteractionModel;

class InteractionModelTest extends IntegrationTestCase
{
    /** @var \Dbt\Interactions\Interaction */
    private $interaction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(InteractionInterface::class, function () {
            return new Interaction(new InteractionModel());
        });

        $this->interaction = new Interaction(new InteractionModel());
    }

    /** @test */
    public function list_all_the_activities()
    {
        $this->app->make(InteractionInterface::class)->in('users')->save('New user added');
        $this->app->make(InteractionInterface::class)->in('not-in-users')->save('New post added');

        $activities = InteractionModel::query()->get();

        $this->assertCount(2, $activities);
        $this->assertInstanceOf(InteractionModel::class, $activities->first());
    }

    /** @test */
    public function scopes_the_activity_by_name()
    {
        $this->app->make(InteractionInterface::class)->in('users')->save('New user added');
        $this->app->make(InteractionInterface::class)->in('not-in-users')->save('New post added');

        $activities = InteractionModel::inLog('users')->get();
        $this->assertCount(1, $activities);
    }

    /** @test */
    public function get_the_property_by_name()
    {
        $properties = ['adjustment' => 12, 'action' => 'Add'];
        $this->app->make(InteractionInterface::class)->with($properties)->save('Inventory updated');

        $activity = InteractionModel::query()->first();

        $this->assertEquals(12, $activity->getExtraProperty('adjustment'));
        $this->assertEquals('Add', $activity->getExtraProperty('action'));
    }

    /** @test */
    public function scope_the_activity_by_causer()
    {
        $john = User::query()->create(['email' => 'John@example.com']);
        $jane = User::query()->create(['email' => 'jane@example.com']);
        $interactionByJohn = $this->app->make(InteractionInterface::class)->by($john)->in('users')->save('New user added');
        $interactionByJane = $this->app->make(InteractionInterface::class)->by($jane)->in('users')->save('New user added');

        $johnsInteractions = InteractionModel::causedBy($john)->get();

        $this->assertCount(1, $johnsInteractions);
    }

    /** @test */
    public function get_the_changes_attribute_return_the_property_with_name_attribute_and_old()
    {
        $properties = [
            'attributes' => ['name' => 'Jane Doe'],
            'old' => ['name' => 'Jane'],
            'other' => 'Other value'
        ];
        $this->app->make(InteractionInterface::class)->with($properties)->save('User name updated');

        $activity = InteractionModel::query()->first();

        $this->assertEquals([
            'attributes' => ['name' => 'Jane Doe'],
            'old' => ['name' => 'Jane'],
        ], $activity->getChangesAttribute()->toArray());
    }
}
