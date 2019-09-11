<?php

namespace Dbt\Interactions\Tests\Feature;

use Dbt\Interactions\Activity;
use Dbt\Interactions\Interaction;
use Dbt\Interactions\Tests\User;

class ActivityTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->interaction = new Interaction(new Activity);
    }

    /** @test */
    public function list_all_the_activities()
    {
        (new Interaction(new Activity))->in('users')->save('New user added');
        (new Interaction(new Activity))->in('not-in-users')->save('New post added');

        $activities = Activity::get();

        $this->assertCount(2, $activities);
        $this->assertInstanceOf(Activity::class, $activities->first());
    }

    /** @test */
    public function scopes_the_activity_by_name()
    {
        (new Interaction(new Activity))->in('users')->save('New user added');
        (new Interaction(new Activity))->in('not-in-users')->save('New post added');

        $activities = Activity::inLog('users')->get();
        $this->assertCount(1, $activities);
    }

    /** @test */
    public function get_the_property_by_name()
    {
        $properties = ['adjustment' => 12, 'action' => 'Add'];
        (new Interaction(new Activity))->with($properties)->save('Inventory updated');

        $activity = Activity::first();

        $this->assertEquals(12, $activity->getExtraProperty('adjustment'));
        $this->assertEquals('Add', $activity->getExtraProperty('action'));
    }

    /** @test */
    public function scope_the_activity_by_causer()
    {
        $john = User::create(['email' => 'John@example.com']);
        $jane = User::create(['email' => 'jane@example.com']);
        $interactionByJohn = (new Interaction(new Activity))->by($john)->in('users')->save('New user added');
        $interactionByJane = (new Interaction(new Activity))->by($jane)->in('users')->save('New user added');

        $johnsInteractions = Activity::causedBy($john)->get();

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
        (new Interaction(new Activity))->with($properties)->save('User name updated');

        $activity = Activity::first();

        $this->assertEquals([
            'attributes' => ['name' => 'Sanjit Singh'],
            'old' => ['name' => 'Sanjit'],
        ], $activity->getChangesAttribute()->toArray());
    }
}
