<?php

namespace Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Workbench\App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench;

    protected ?User $user = null;

    protected function setUp(): void
    {
        // Always show errors when testing
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        parent::setUp();

        $this->withoutVite();
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $token = getenv('TEST_TOKEN') ?: 'default';
        $path = $app->storagePath("framework/views/$token");

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $app['config']->set('view.compiled', $path);
    }

    /**
     * Run database migrations
     */
    protected function migrate(): self
    {
        $this->artisan('migrate');

        return $this;
    }

    /**
     * Set up a user
     */
    protected function setUpUser(): self
    {
        $this->user = User::factory()->create();

        $this->actingAs($this->user);

        return $this;
    }

    public function ignorePackageDiscoveriesFrom()
    {
        return [
            // A list of packages that should not be auto-discovered when running tests
            'laravel/telescope',
        ];
    }
}
