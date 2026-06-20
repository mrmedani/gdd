<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::firstOrCreate(['name' => 'accountant'], ['label_ar' => 'محاسب', 'label_fr' => 'Comptable']);
        $this->user = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_reports_index_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertOk();
    }

    public function test_monthly_pdf_download(): void
    {
        $category = ExpenseCategory::factory()->create();
        Expense::factory()->create([
            'category_id' => $category->id,
            'created_by' => $this->user->id,
            'date' => now(),
        ]);

        $response = $this->actingAs($this->user)->post(route('reports.monthly.pdf'), [
            'month' => now()->month,
            'year' => now()->year,
        ]);

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_annual_pdf_download(): void
    {
        $category = ExpenseCategory::factory()->create();
        Expense::factory()->create([
            'category_id' => $category->id,
            'created_by' => $this->user->id,
            'date' => now(),
        ]);

        $response = $this->actingAs($this->user)->post(route('reports.annual.pdf'), [
            'year' => now()->year,
        ]);

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_monthly_excel_download(): void
    {
        $category = ExpenseCategory::factory()->create();
        Expense::factory()->create([
            'category_id' => $category->id,
            'created_by' => $this->user->id,
            'date' => now(),
        ]);

        $response = $this->actingAs($this->user)->post(route('reports.monthly.excel'), [
            'month' => now()->month,
            'year' => now()->year,
        ]);

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
    }

    public function test_report_validation_fails_without_month(): void
    {
        $response = $this->actingAs($this->user)->post(route('reports.monthly.pdf'), [
            'year' => now()->year,
        ]);

        $response->assertSessionHasErrors('month');
    }

    public function test_report_validation_fails_with_invalid_year(): void
    {
        $response = $this->actingAs($this->user)->post(route('reports.monthly.pdf'), [
            'month' => 1,
            'year' => 1999,
        ]);

        $response->assertSessionHasErrors('year');
    }
}
