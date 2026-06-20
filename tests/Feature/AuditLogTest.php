<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Domains\Expenses\Models\AuditLog;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ExpenseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::firstOrCreate(['name' => 'accountant'], ['label_ar' => 'محاسب', 'label_fr' => 'Comptable']);
        $this->user = User::factory()->create(['role_id' => $role->id]);
        $this->category = ExpenseCategory::factory()->create();
    }

    public function test_creates_audit_log_on_expense_creation(): void
    {
        $this->actingAs($this->user);

        $expense = Expense::factory()->create([
            'category_id' => $this->category->id,
            'category_key' => 'fuel',
            'created_by' => $this->user->id,
        ]);

        $log = AuditLog::where('entity_type', 'expense')
            ->where('action', 'created')
            ->where('entity_id', $expense->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($this->user->id, $log->user_id);
    }

    public function test_creates_audit_log_on_expense_update(): void
    {
        $expense = Expense::factory()->create([
            'category_id' => $this->category->id,
            'category_key' => 'fuel',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user);
        $expense->update(['amount' => 9999]);

        $log = AuditLog::where('entity_type', 'expense')
            ->where('action', 'updated')
            ->where('entity_id', $expense->id)
            ->first();

        $this->assertNotNull($log);
    }

    public function test_creates_audit_log_on_expense_deletion(): void
    {
        $expense = Expense::factory()->create([
            'category_id' => $this->category->id,
            'category_key' => 'fuel',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user);
        $expense->delete();

        $log = AuditLog::where('entity_type', 'expense')
            ->where('action', 'deleted')
            ->where('entity_id', $expense->id)
            ->first();

        $this->assertNotNull($log);
    }
}
