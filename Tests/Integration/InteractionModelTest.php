<?php

namespace Dbt\Interactions\Tests\Integration;

use Dbt\Interactions\InteractionModel;
use Dbt\Interactions\Interaction;
use Dbt\Interactions\Tests\Common\IntegrationTestCase;
use Dbt\Interactions\Tests\Common\Fixtures\User;

class InteractionModelTest extends IntegrationTestCase
{
    /** @var \Dbt\Interactions\Interaction */
    private $interaction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->interaction = new Interaction(new InteractionModel());
    }

    /** @test */
    public function list_all_the_activities()
    {
        (new Interaction(new InteractionModel))->in('users')->save('New user added');
        (new Interaction(new InteractionModel))->in('not-in-users')->save('New post added');

        $activities = InteractionModel::query()->get();

        $this->assertCount(2, $activities);
        $this->assertInstanceOf(InteractionModel::class, $activities->first());
    }

    /** @test */
    public function scopes_the_activity_by_name()
    {
        (new Interaction(new InteractionModel))->in('users')->save('New user added');
        (new Interaction(new InteractionModel))->in('not-in-users')->save('New post added');

        $activities = InteractionModel::inLog('users')->get();
        $this->assertCount(1, $activities);
    }

    /** @test */
    public function get_the_property_by_name()
    {
        $properties = ['adjustment' => 12, 'action' => 'Add'];
        (new Interaction(new InteractionModel))->with($properties)->save('Inventory updated');

        $activity = InteractionModel::query()->first();

        $this->assertEquals(12, $activity->getExtraProperty('adjustment'));
        $this->assertEquals('Add', $activity->getExtraProperty('action'));
    }

    /** @test */
    public function scope_the_activity_by_causer()
    {
        $john = User::query()->create(['email' => 'John@example.com']);
        $jane = User::query()->create(['email' => 'jane@example.com']);
        $interactionByJohn = (new Interaction(new InteractionModel))->by($john)->in('users')->save('New user added');
        $interactionByJane = (new Interaction(new InteractionModel))->by($jane)->in('users')->save('New user added');

        $johnsInteractions = InteractionModel::causedBy($john)->get();

        $this->assertCount(1, $johnsInteractions);
    }

    /** @test */
    public function get_the_changes_attribute_return_the_property_with_name_attribute_and_old()
    {
        $properties = [
            'attributes' => ['name' => 'Sanjit Singh'],
            'old' => ['name' => 'Sanjit'],
            'other' => 'Other value'
        ];
        (new Interaction(new InteractionModel))->with($properties)->save('User name updated');

        $activity = InteractionModel::query()->first();

        $this->assertEquals([
            'attributes' => ['name' => 'Sanjit Singh'],
            'old' => ['name' => 'Sanjit'],
        ], $activity->getChangesAttribute()->toArray());
    }
}
