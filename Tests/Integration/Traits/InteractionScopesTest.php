<?php

namespace Dbt\Interactions\Tests\Integration\Traits;

use Dbt\Interactions\Contracts\InteractionInterface;
use Dbt\Interactions\Tests\Support\Fixtures\Interaction as InteractionModel;
use Dbt\Interactions\Interaction;
use Dbt\Interactions\Tests\Support\Fixtures\Employee;
use Dbt\Interactions\Tests\Support\Fixtures\Logs\PostLog;
use Dbt\Interactions\Tests\Support\Fixtures\Logs\UserLog;
use Dbt\Interactions\Tests\Support\Fixtures\User;
use  Dbt\Interactions\Tests\Support\IntegrationTestCase;

class InteractionScopesTest extends IntegrationTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->app->bind(InteractionInterface::class, function () {
            return new Interaction(new InteractionModel());
        });
    }

    /** @test */
    public function scopes_the_interactions_by_log()
    {
        $userLog = new UserLog;

        $this->app->make(InteractionInterface::class)->in($userLog)->save('New user added');
        $this->app->make(InteractionInterface::class)->in(new PostLog)->save('New post added');

        $activities = InteractionModel::inLog($userLog)->get();
        $this->assertCount(1, $activities);
    }

    /** @test */
    public function scope_the_interactions_by_causer()
    {
        $john = User::query()->create(['email' => 'John@example.com']);
        $jane = User::query()->create(['email' => 'jane@example.com']);
        $interactionByJohn = $this->app->make(InteractionInterface::class)->by($john)->in(new UserLog)->save('New user added');
        $interactionByJane = $this->app->make(InteractionInterface::class)->by($jane)->in(new UserLog)->save('New user added');

        $johnsInteractions = InteractionModel::causedBy($john)->get();

        $this->assertCount(1, $johnsInteractions);
    }

    /** @test */
    public function scope_the_interactions_by_subject()
    {
        $this->app->make(InteractionInterface::class)->on($this->testUser)->save('New user added');
        $this->app->make(InteractionInterface::class)->on($this->testPost)->save('New post added');

        $interactionsOnUser = InteractionModel::forSubject($this->testUser)->get();
        $this->assertCount(1, $interactionsOnUser);
    }

    /** @test */
    public function scope_interactions_by_causer_type()
    {
        $john = User::query()->create(['email' => 'John@example.com']);
        $jane = User::query()->create(['email' => 'jane@example.com']);
        $employee = Employee::query()->create(['email' => 'test-employee@example.com']);

        $this->app->make(InteractionInterface::class)->by($john)->in(new UserLog)->save('New user added');
        $this->app->make(InteractionInterface::class)->by($jane)->in(new UserLog)->save('New user added');
        $this->app->make(InteractionInterface::class)->by($employee)->in(new UserLog)->save('New user added');

        $interactionsByUser = InteractionModel::causerIs(User::class)->get();

        $this->assertCount(2, $interactionsByUser);
    }
}
