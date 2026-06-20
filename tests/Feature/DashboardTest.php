<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::firstOrCreate(['name' => 'accountant'], ['label_ar' => 'محاسب', 'label_fr' => 'Comptable']);
        $this->user = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_dashboard_loads_with_expense_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_dashboard_contains_livewire_component(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertSee('Chronorex Express');
    }
}
