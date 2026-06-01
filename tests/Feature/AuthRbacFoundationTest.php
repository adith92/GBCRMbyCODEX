<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRbacFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('email', 'gm@blueerp.test')->firstOrFail();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $user = User::where('email', 'superadmin@blueerp.test')->firstOrFail();

        foreach (config('rbac.permissions') as $permission) {
            $this->assertTrue($user->can($permission), "Super admin missing permission: {$permission}");
        }
    }

    public function test_pool_staff_does_not_have_finance_permissions(): void
    {
        $user = User::where('email', 'poolstaff@blueerp.test')->firstOrFail();

        $this->assertFalse($user->can('invoices.view'));
        $this->assertFalse($user->can('payments.create'));
        $this->assertFalse($user->can('purchase-orders.approve'));
    }

    public function test_finance_does_not_have_pool_assign_driver_permission(): void
    {
        $user = User::where('email', 'finance@blueerp.test')->firstOrFail();

        $this->assertFalse($user->can('pool.assign-driver'));
    }

    public function test_non_super_admin_cannot_access_hr_route(): void
    {
        $user = User::where('email', 'gm@blueerp.test')->firstOrFail();

        $response = $this->actingAs($user)->get('/admin/hr');

        $response->assertForbidden();
    }
}
