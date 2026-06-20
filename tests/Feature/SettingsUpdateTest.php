<?php

namespace Tests\Feature;

use App\Domains\Settings\Models\Setting;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Domains\Settings\Livewire\Settings;

class SettingsUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_can_be_updated_via_livewire(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin'], [
            'label_ar' => 'مدير',
            'label_fr' => 'Admin',
            'label_en' => 'Admin',
        ]);
        $admin = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($admin);

        Setting::set('currency', 'MAD');
        $this->assertEquals('MAD', Setting::get('currency'));

        Livewire::test(Settings::class)
            ->set('currency', 'EUR')
            ->call('updateCurrency')
            ->assertHasNoErrors()
            ->assertRedirect(route('settings.index'));

        $this->assertEquals('EUR', Setting::get('currency'));
    }
}
