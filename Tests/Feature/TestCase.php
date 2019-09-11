<?php

namespace Dbt\Interactions\Tests\Feature;

use Dbt\Interactions\Providers\InteractionServiceProvider;
use Dbt\Interactions\Providers\MigrationsProvider;
use  Illuminate\Database\Schema\Blueprint;
use Dbt\Interactions\Tests\User;
use Dbt\Interactions\Tests\Post;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /** @var Dbt\Interactions\Tests\User */
    protected $testUser;

    /** @var Dbt\Interactions\Tests\Post */
    protected $testPost;

    /** @var string */
    protected $interactionTable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase($this->app);

        $this->testUser = User::first();
        $this->testPost = Post::first();
        $this->interactionTable = config('interaction.table_name');
    }

    protected function getPackageProviders($app): array
    {
        return [
            MigrationsProvider::class,
            InteractionServiceProvider::class,
        ];
    }

    private function setupDatabase($app)
    {
        // Users table
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->softDeletes();
        });
        User::create(['email' => 'test-user@example.com']);


        // Posts Table
        $app['db']->connection()->getSchemaBuilder()->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        });
        Post::create(['title' => 'Test Post']);

        // Interactions table
        include_once __DIR__ . '/../../Migrations/create_interactions_table.php.stub';
        (new \CreateInteractionsTable())->up();
    }


    protected function assertInteractionsTableHas($attributes)
    {
        $this->assertDatabaseHas($this->interactionTable, $attributes);
    }
}
