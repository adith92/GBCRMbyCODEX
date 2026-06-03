<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VendorPartner;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PremiumWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        putenv('ENABLE_DEMO_SEED=true');
        $_ENV['ENABLE_DEMO_SEED'] = 'true';
        $_SERVER['ENABLE_DEMO_SEED'] = 'true';

        $this->seed(DatabaseSeeder::class);
    }

    public function test_gm_can_view_reports_dashboard(): void
    {
        $user = User::query()->where('email', 'gm@blueerp.test')->firstOrFail();

        $this->actingAs($user)
            ->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Reports & Insights');
    }

    public function test_sales_user_can_view_partner_vendor_module(): void
    {
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();

        $this->actingAs($user)
            ->get(route('partners.vendors.index'))
            ->assertOk()
            ->assertSee('Partner & Vendor Network');
    }

    public function test_partner_results_appear_in_search_scope(): void
    {
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();
        $partner = VendorPartner::query()->create([
            'code' => 'VP-TEST-001',
            'name' => 'Partner Search Alpha',
            'category' => 'partner',
            'service_type' => 'Workshop',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('search.index', ['q' => 'Partner Search', 'scope' => 'partner']))
            ->assertOk()
            ->assertSee($partner->name);
    }

    public function test_demo_role_switcher_allows_switching_to_finance_user(): void
    {
        $superAdmin = User::query()->where('email', 'superadmin@blueerp.test')->firstOrFail();

        $response = $this->actingAs($superAdmin)
            ->post(route('demo.switch-role'), ['email' => 'finance@blueerp.test']);

        $response->assertRedirect();
        $this->assertAuthenticatedAs(User::query()->where('email', 'finance@blueerp.test')->firstOrFail());
    }
}
