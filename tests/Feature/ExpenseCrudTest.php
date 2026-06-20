<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseCrudTest extends TestCase
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

    public function test_can_view_expenses_list(): void
    {
        $response = $this->actingAs($this->user)->get(route('expenses.index'));
        $response->assertOk();
    }

    public function test_can_view_create_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('expenses.create'));
        $response->assertOk();
    }

    public function test_can_create_expense(): void
    {
        $data = [
            'date' => now()->format('Y-m-d'),
            'amount' => 1500.50,
            'category_id' => $this->category->id,
            'description' => 'Test expense',
            'payment_method' => 'cash',
        ];

        Expense::create(array_merge($data, [
            'category_key' => 'fuel',
            'created_by' => $this->user->id,
        ]));

        $this->assertDatabaseHas('expenses', ['description' => 'Test expense']);
    }

    public function test_can_update_expense(): void
    {
        $expense = Expense::factory()->create(['created_by' => $this->user->id]);

        $expense->update(['amount' => 2500]);

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'amount' => 2500]);
    }

    public function test_can_soft_delete_expense(): void
    {
        $expense = Expense::factory()->create(['created_by' => $this->user->id]);

        $expense->delete();

        $this->assertSoftDeleted($expense);
    }
}
