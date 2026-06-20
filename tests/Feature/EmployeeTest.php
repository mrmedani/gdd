<?php

namespace Tests\Feature;

use App\Domains\Employees\Models\Employee;
use App\Domains\Employees\Models\SalaryAdvance;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Domains\Employees\Livewire\EmployeeList;
use App\Domains\Employees\Livewire\EmployeeForm;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $accountant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'admin'], [
            'label_ar' => 'مدير',
            'label_fr' => 'Admin',
            'label_en' => 'Admin'
        ]);
        $accRole = Role::firstOrCreate(['name' => 'accountant'], [
            'label_ar' => 'محاسب',
            'label_fr' => 'Comptable',
            'label_en' => 'Accountant'
        ]);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->accountant = User::factory()->create(['role_id' => $accRole->id]);
    }

    public function test_admin_can_view_employees()
    {
        $this->actingAs($this->admin)
             ->get(route('employees.index'))
             ->assertStatus(200);
    }

    public function test_accountant_can_view_employees()
    {
        $this->actingAs($this->accountant)
             ->get(route('employees.index'))
             ->assertStatus(200);
    }

    public function test_can_create_employee_via_livewire()
    {
        $this->actingAs($this->admin);

        Livewire::test(EmployeeForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('phone', '123456789')
            ->set('base_salary', '5000')
            ->set('hired_at', '2026-05-20')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('employees', [
            'name' => 'John Doe',
            'base_salary' => 5000,
        ]);
    }

    public function test_can_add_salary_advance()
    {
        $this->actingAs($this->accountant);

        $employee = Employee::create([
            'name' => 'Jane Doe',
            'base_salary' => 6000,
            'status' => 'active',
        ]);

        Livewire::test(EmployeeForm::class, ['employee' => $employee])
            ->set('activeTab', 'advances')
            ->set('advanceAmount', '1000')
            ->set('advanceDate', '2026-05-15')
            ->call('createAdvance')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('salary_advances', [
            'employee_id' => $employee->id,
            'amount' => 1000,
            'status' => 'approved',
        ]);
    }

    public function test_can_log_salary_payment_and_deduct_advance()
    {
        $this->actingAs($this->accountant);

        $employee = Employee::create([
            'name' => 'Jane Doe',
            'base_salary' => 6000,
            'status' => 'active',
        ]);

        $advance = SalaryAdvance::create([
            'employee_id' => $employee->id,
            'amount' => 1000,
            'date' => '2026-05-15',
            'status' => 'approved',
            'created_by' => $this->accountant->id,
        ]);

        Livewire::test(EmployeeForm::class, ['employee' => $employee])
            ->set('activeTab', 'payments')
            ->set('paymentMonth', '5')
            ->set('paymentYear', '2026')
            ->set('paymentDeduction', '1000')
            ->call('createPayment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('salary_payments', [
            'employee_id' => $employee->id,
            'base_amount' => 6000,
            'advances_deducted' => 1000,
            'net_amount' => 5000,
        ]);

        $this->assertDatabaseHas('salary_advances', [
            'id' => $advance->id,
            'status' => 'deducted',
        ]);
    }

    public function test_salary_reminder_command()
    {
        Employee::create([
            'name' => 'Mark Smith',
            'base_salary' => 7000,
            'status' => 'active',
        ]);

        $this->artisan('alerts:salary-reminders')
             ->assertSuccessful();

        $this->assertDatabaseHas('alerts', [
            'type' => 'salary_reminder',
        ]);
    }
}
