<?php

namespace Tests\Unit;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_belongs_to_category(): void
    {
        $category = ExpenseCategory::factory()->create();
        $expense = Expense::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(ExpenseCategory::class, $expense->category);
        $this->assertEquals($category->id, $expense->category->id);
    }

    public function test_expense_belongs_to_creator(): void
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $expense->creator);
        $this->assertEquals($user->id, $expense->creator->id);
    }

    public function test_expense_scope_by_month(): void
    {
        Expense::factory()->create(['date' => '2026-05-15']);
        Expense::factory()->create(['date' => '2026-04-10']);
        Expense::factory()->create(['date' => '2026-05-20']);

        $count = Expense::byMonth(2026, 5)->count();

        $this->assertEquals(2, $count);
    }

    public function test_expense_scope_by_category(): void
    {
        Expense::factory()->create(['category_key' => 'fuel']);
        Expense::factory()->create(['category_key' => 'fuel']);
        Expense::factory()->create(['category_key' => 'rent']);

        $count = Expense::byCategory('fuel')->count();

        $this->assertEquals(2, $count);
    }

    public function test_expense_scope_amount_between(): void
    {
        Expense::factory()->create(['amount' => 500]);
        Expense::factory()->create(['amount' => 1500]);
        Expense::factory()->create(['amount' => 3000]);

        $count = Expense::amountBetween(1000, 2000)->count();

        $this->assertEquals(1, $count);
    }
}
