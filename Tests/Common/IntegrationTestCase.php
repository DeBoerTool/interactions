<?php

namespace Dbt\Interactions\Tests\Common;

use Closure;
use CreateInteractionsTable;
use Dbt\Interactions\Providers\InteractionServiceProvider;
use Dbt\Interactions\Providers\MigrationsProvider;
use Dbt\Interactions\Tests\Common\Fixtures\User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Dbt\Interactions\Tests\Common\Fixtures\Post;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    use LocalAssertions;

    /** @var \Dbt\Interactions\Tests\Common\Fixtures\User */
    protected $testUser;

    /** @var \Dbt\Interactions\Tests\Common\Fixtures\Post */
    protected $testPost;

    /** @var string */
    protected $database = '';

    /** @var string */
    protected $table = '';

    /** @var string */
    protected $interactionTable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->database = Str::random(16);
        $this->table = Str::random(16);

        $this->setupDatabase();
        $this->migrateDatabase();
        $this->createModels();

        $this->testUser = User::query()->first();
        $this->testPost = Post::query()->first();

        $this->interactionTable = config('interaction.table_name');
    }

    protected function getPackageProviders($app): array
    {
        return [
            MigrationsProvider::class,
            InteractionServiceProvider::class,
        ];
    }

    private function setupDatabase()
    {
        $this->configureDatabase($this->app->get('config'));

        // Interactions table
        include_once __DIR__ . '/../../Migrations/create_interactions_table.php.stub';
    }

    private function createModels()
    {
        foreach (self::models() as $params) {
            self::createModel(...$params);
        }
    }

    private function configureDatabase(Repository $config): void
    {
        $config->set('interaction.table_name', $this->table);
        $config->set('interaction.database_connection', $this->database);
        $config->set('interaction.logs', [
            \Dbt\Interactions\Log::class,
            \Dbt\Interactions\Tests\Common\Fixtures\Logs\UserLog::class,
            \Dbt\Interactions\Tests\Common\Fixtures\Logs\PostLog::class,
            \Dbt\Interactions\Tests\Common\Fixtures\Logs\InventoryUpdateLog::class,
        ]);
        $config->set('database.default', $this->database);
        $config->set('database.connections.' . $this->database, [
            'driver' => env('DB_DRIVER'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'prefix' => env('DB_PASSWORD'),
        ]);
    }

    private function migrateDatabase()
    {
        (new CreateInteractionsTable())->up();

        foreach (self::migrations() as $params) {
            self::migrate($this->app->get('db'), ...$params);
        }
    }

    private static function migrations(): array
    {
        return [
            ['users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('email');
                $table->softDeletes();
            }],
            ['posts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
            }],
        ];
    }

    private static function models(): array
    {
        return [
            [new User(), ['email' => 'test-user@example.com']],
            [new Post(), ['title' => 'Test Post']],
        ];
    }

    private static function migrate(DB $db, string $name, Closure $migration): void
    {
        $db->getSchemaBuilder()->create($name, $migration);
    }

    private static function createModel(Model $model, array $attrs): void
    {
        $model::query()->create($attrs);
    }
}
