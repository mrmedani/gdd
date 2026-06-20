<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $accountant;

    protected function setUp(): void
    {
        parent::setUp();
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label_ar' => 'مدير', 'label_fr' => 'Admin']);
        $accountantRole = Role::firstOrCreate(['name' => 'accountant'], ['label_ar' => 'محاسب', 'label_fr' => 'Comptable']);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->accountant = User::factory()->create(['role_id' => $accountantRole->id]);
    }

    public function test_admin_can_access_settings(): void
    {
        $response = $this->actingAs($this->admin)->get(route('settings.index'));
        $response->assertOk();
    }

    public function test_accountant_cannot_access_settings(): void
    {
        $response = $this->actingAs($this->accountant)->get(route('settings.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_access_users_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('settings.users'));
        $response->assertOk();
    }

    public function test_accountant_cannot_access_users_page(): void
    {
        $response = $this->actingAs($this->accountant)->get(route('settings.users'));
        $response->assertForbidden();
    }

    public function test_accountant_can_access_expenses(): void
    {
        $response = $this->actingAs($this->accountant)->get(route('expenses.index'));
        $response->assertOk();
    }
}
