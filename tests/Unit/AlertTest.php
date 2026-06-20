<?php

namespace Tests\Unit;

use App\Domains\Alerts\Models\Alert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_alert(): void
    {
        $alert = Alert::create([
            'type' => 'high_expense',
            'message_ar' => 'مصروف مرتفع',
            'message_fr' => 'Dépense élevée',
            'severity' => 'danger',
        ]);

        $this->assertDatabaseHas('alerts', ['type' => 'high_expense']);
    }

    public function test_scope_unread(): void
    {
        Alert::create(['type' => 'test', 'message_ar' => 'a', 'message_fr' => 'a', 'is_read' => false]);
        Alert::create(['type' => 'test', 'message_ar' => 'b', 'message_fr' => 'b', 'is_read' => true]);

        $this->assertEquals(1, Alert::unread()->count());
    }

    public function test_mark_as_read(): void
    {
        $alert = Alert::create([
            'type' => 'salary_reminder',
            'message_ar' => 'تذكير',
            'message_fr' => 'Rappel',
        ]);

        $alert->update(['is_read' => true, 'read_at' => now()]);

        $this->assertTrue($alert->fresh()->is_read);
        $this->assertNotNull($alert->fresh()->read_at);
    }
}
