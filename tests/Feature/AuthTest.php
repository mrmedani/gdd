<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::firstOrCreate(['name' => 'accountant'], ['label_ar' => 'محاسب', 'label_fr' => 'Comptable']);
        $this->user = User::factory()->create([
            'role_id' => $role->id,
            'password' => bcrypt('password'),
        ]);
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertOk();
    }

    public function test_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_cannot_login_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_sees_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_guest_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_can_logout(): void
    {
        $response = $this->actingAs($this->user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
