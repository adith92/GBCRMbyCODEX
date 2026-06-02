<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScalableDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected array $originalEnv = [];

    protected function tearDown(): void
    {
        $this->restoreDemoSeedEnv();

        parent::tearDown();
    }

    public function test_demo_seed_mode_creates_at_least_ten_clients(): void
    {
        $this->setDemoSeedEnv([
            'ENABLE_DEMO_SEED' => 'true',
            'DEMO_SEED_MODE' => 'demo',
            'DEMO_CUSTOMER_COUNT' => '15',
        ]);

        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(10, Client::query()->count());
    }

    public function test_stress_seed_mode_creates_at_least_one_thousand_clients(): void
    {
        $this->setDemoSeedEnv([
            'ENABLE_DEMO_SEED' => 'true',
            'DEMO_SEED_MODE' => 'stress',
            'DEMO_CUSTOMER_COUNT' => '1000',
        ]);

        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(1000, Client::query()->count());
    }

    public function test_client_index_pagination_and_search_work_with_stress_data(): void
    {
        $this->setDemoSeedEnv([
            'ENABLE_DEMO_SEED' => 'true',
            'DEMO_SEED_MODE' => 'stress',
            'DEMO_CUSTOMER_COUNT' => '1000',
        ]);

        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'salesmanager@blueerp.test')->firstOrFail();

        $this->actingAs($user)
            ->get('/crm/clients?search=Stress%20Client%20000001')
            ->assertOk()
            ->assertSee('Stress Client 000001');

        $this->actingAs($user)
            ->get('/crm/clients?page=2')
            ->assertOk();
    }

    protected function setDemoSeedEnv(array $values): void
    {
        foreach ($values as $key => $value) {
            if (! array_key_exists($key, $this->originalEnv)) {
                $this->originalEnv[$key] = env($key);
            }

            putenv($key.'='.$value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    protected function restoreDemoSeedEnv(): void
    {
        foreach ($this->originalEnv as $key => $value) {
            if ($value === null || $value === false) {
                putenv($key);
                unset($_ENV[$key], $_SERVER[$key]);

                continue;
            }

            putenv($key.'='.$value);
            $_ENV[$key] = (string) $value;
            $_SERVER[$key] = (string) $value;
        }

        $this->originalEnv = [];
    }
}
