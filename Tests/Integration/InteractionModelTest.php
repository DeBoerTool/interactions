<?php

namespace Dbt\Interactions\Tests\Integration;

use Dbt\Interactions\Interaction;
use Dbt\Interactions\Tests\Common\Fixtures\User;
use Dbt\Interactions\Contracts\InteractionInterface;
use Dbt\Interactions\Tests\Common\IntegrationTestCase;
use Dbt\Interactions\Tests\Common\Fixtures\Interaction as InteractionModel;
use Dbt\Interactions\Tests\Common\Fixtures\Logs\PostLog;
use Dbt\Interactions\Tests\Common\Fixtures\Logs\UserLog;
use Dbt\Interactions\Tests\Common\Fixtures\Post;

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
    }

    /** @test */
    public function list_all_the_activities()
    {
        $this->app->make(InteractionInterface::class)->in(new UserLog)->save('New user added');
        $this->app->make(InteractionInterface::class)->in(new PostLog)->save('New post added');

        $activities = InteractionModel::query()->get();

        $this->assertCount(2, $activities);
        $this->assertInstanceOf(InteractionModel::class, $activities->first());
    }



    /** @test */
    public function get_the_property_by_name()
    {
        $properties = ['adjustment' => 12, 'action' => 'Add'];

        $interactionModel = new InteractionModel([
            'properties' => collect($properties),
        ]);

        $this->assertEquals(12, $interactionModel->property('adjustment'));
        $this->assertEquals('Add', $interactionModel->property('action'));
    }

    /** @test */
    public function get_all_the_properties_of_interaction()
    {
        $properties = ['adjustment' => 12, 'action' => 'Add'];

        $interactionModel = new InteractionModel([
            'properties' => collect($properties),
        ]);

        $this->assertEquals($properties, $interactionModel->properties()->toArray());
    }

    /** @test */
    public function get_the_log_of_the_interaction()
    {
        $userLog = new UserLog;
        $interactionModel = new InteractionModel([
            'log_name' => $userLog->getName(),
        ]);

        $this->assertInstanceOf(UserLog::class, $interactionModel->log());
    }

    /** @test */
    public function get_the_description_of_the_interaction()
    {
        $interactionModel = new InteractionModel([
            'description' => 'Interaction description'
        ]);
        $this->assertEquals('Interaction description', $interactionModel->description());
    }

    /** @test */
    public function set_the_description_of_interaction()
    {
        $interactionModel = new InteractionModel();
        $interactionModel->setDescription('Interaction description');

        $this->assertEquals('Interaction description', $interactionModel->description());
    }

    /** @test */
    public function set_the_properties_of_interaction()
    {
        $properties = collect(['adjustment' => 12, 'action' => 'Add']);
        $interactionModel = new InteractionModel();
        $interactionModel->setProperties($properties);

        $this->assertEquals($properties, $interactionModel->properties());
    }

    /** @test */
    public function get_the_changes_attribute_return_the_property_with_name_attribute_and_old()
    {
        $properties = [
            'attributes' => ['name' => 'Jane Doe'],
            'old' => ['name' => 'Jane'],
            'other' => 'Other value'
        ];

        $interactionModel = new InteractionModel([
            'properties' => $properties
        ]);

        $this->assertEquals([
            'attributes' => ['name' => 'Jane Doe'],
            'old' => ['name' => 'Jane'],
        ], $interactionModel->getChangesAttribute()->toArray());
    }

    /** @test */
    public function set_the_log_of_the_interaction()
    {
        $userLog = new UserLog;
        $interactionModel = new InteractionModel();
        $interactionModel->setLog($userLog);

        $this->assertInstanceOf(UserLog::class, $interactionModel->log());
    }

    /** @test */
    public function access_the_properties_using_dot_notation()
    {
        $properties = [
            'attributes' => ['name' => 'Jane Doe'],
            'old' => ['name' => 'Jane'],
            'other' => 'Other value'
        ];

        $interactionModel = new InteractionModel([
            'properties' => $properties
        ]);

        $this->assertEquals('Jane Doe', $interactionModel->getExtraProperty('attributes.name'));
    }

    /** @test */
    public function interaction_can_have_polymorphic_subject()
    {
        $interactionA = $this->app->make(InteractionInterface::class)
            ->on($this->testPost)
            ->save('Post Updated');

        $interactionB = $this->app->make(InteractionInterface::class)
            ->on($this->testUser)
            ->save('User Updated');

        $this->assertInstanceOf(Post::class, $interactionA->subject);
        $this->assertInstanceOf(User::class, $interactionB->subject);
    }

    /** @test */
    public function interaction_can_have_polymorphic_causer()
    {
        $interactionB = $this->app->make(InteractionInterface::class)
            ->by($this->testUser)
            ->save('User Updated');

        $this->assertInstanceOf(User::class, $interactionB->causer);
    }
}
