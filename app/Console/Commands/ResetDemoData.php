<?php

namespace App\Console\Commands;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Employees\Models\Employee;
use App\Domains\Employees\Models\SalaryAdvance;
use App\Domains\Employees\Models\SalaryPayment;
use App\Domains\Expenses\Models\AuditLog;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Console\Command;

class ResetDemoData extends Command
{
    protected $signature = 'demo:reset';
    protected $description = 'Supprime toutes les données de démo tout en préservant les administrateurs et les paramètres';

    public function handle(): void
    {
        if (!$this->confirm('⚠️  Cette action va supprimer TOUTES les données (dépenses, employés, alertes, catégories, audit logs). Les comptes admin/accountant et les paramètres seront conservés. Continuer ?')) {
            $this->info('Annulé.');
            return;
        }

        $this->warn('Suppression des données...');

        SalaryPayment::truncate();
        SalaryAdvance::truncate();
        Employee::truncate();
        Alert::truncate();
        AuditLog::truncate();
        Expense::truncate();

        $deleted = ExpenseCategory::whereNotIn('key', ['salaries', 'rent', 'utilities', 'supplies', 'transport', 'maintenance', 'marketing'])->delete();

        $this->info('✅ Données de démo supprimées avec succès !');
        $this->info('   - Dépenses: supprimées');
        $this->info('   - Employés: supprimés');
        $this->info('   - Alertes: supprimées');
        $this->info('   - Audit logs: supprimés');
        $this->info('   - Catégories personnalisées: ' . ($deleted ? "$deleted supprimées" : 'aucune'));
        $this->info('   - Comptes administrateur et paramètres: conservés');
    }
}
