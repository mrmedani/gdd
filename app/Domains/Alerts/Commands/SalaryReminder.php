<?php

namespace App\Domains\Alerts\Commands;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Alerts\Notifications\SalaryReminderNotification;
use App\Domains\Employees\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SalaryReminder extends Command
{
    protected $signature = 'alerts:salary-reminders';
    protected $description = 'Send salary payout reminders to admins';

    public function handle(): int
    {
        $activeEmployees = Employee::where('status', 'active')->count();
        $totalBaseSalary = Employee::where('status', 'active')->sum('base_salary');

        if ($activeEmployees === 0) {
            $this->info('No active employees for salary reminder.');
            return self::SUCCESS;
        }

        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();

        if (!Alert::alreadySentToday('salary_reminder')) {
            Alert::create([
                'type' => 'salary_reminder',
                'message_ar' => "تذكير: حان موعد صرف الرواتب الشهري لـ {$activeEmployees} موظف بإجمالي {$totalBaseSalary} " . getCurrency(),
                'message_fr' => "Rappel: C'est le jour de paie pour {$activeEmployees} employés avec un total de {$totalBaseSalary} " . getCurrency(),
                'severity' => 'info',
            'data' => [
                'count' => $activeEmployees,
                'total_salary' => $totalBaseSalary,
                'action_url' => url('/employees'),
                'action_label' => 'Voir les employés',
            ],
            ]);
        }

        Notification::send($admins, new SalaryReminderNotification($activeEmployees, $totalBaseSalary));

        $this->info("Salary reminder generated for {$activeEmployees} employees.");

        return self::SUCCESS;
    }
}
