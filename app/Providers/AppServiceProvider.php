<?php

namespace App\Providers;

use App\Domains\Auth\Livewire\ForgotPassword;
use App\Domains\Auth\Livewire\ResetPassword;
use App\Domains\Dashboard\Livewire\Dashboard;
use App\Domains\Expenses\Livewire\ExpenseForm;
use App\Domains\Expenses\Livewire\ExpenseList;
use App\Domains\Employees\Models\Employee;
use App\Domains\Employees\Models\SalaryAdvance;
use App\Domains\Employees\Models\SalaryPayment;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Domains\Expenses\Observers\ExpenseObserver;
use App\Domains\Settings\Livewire\AuditLogs;
use App\Domains\Settings\Observers\EmployeeObserver;
use App\Domains\Settings\Observers\ExpenseCategoryObserver;
use App\Domains\Settings\Observers\MonthlyClosureObserver;
use App\Domains\Settings\Observers\RoleObserver;
use App\Domains\Settings\Observers\SalaryAdvanceObserver;
use App\Domains\Settings\Observers\SalaryPaymentObserver;
use App\Domains\Settings\Observers\UserObserver;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Models\Role;
use App\Models\User;
use App\Domains\Settings\Livewire\Categories;
use App\Domains\Settings\Livewire\EmailTemplates;
use App\Domains\Settings\Livewire\Roles;
use App\Domains\Settings\Livewire\Settings;
use App\Domains\Settings\Livewire\Users;
use App\Domains\Settings\Models\Setting;
use App\Domains\Treasury\Livewire\TreasuryIndex;
use App\Livewire\ProfileSettings;
use App\Policies\ExpensePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Shared/Helpers/helpers.php');
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Expense::observe(ExpenseObserver::class);
        ExpenseCategory::observe(ExpenseCategoryObserver::class);
        Employee::observe(EmployeeObserver::class);
        SalaryAdvance::observe(SalaryAdvanceObserver::class);
        SalaryPayment::observe(SalaryPaymentObserver::class);
        MonthlyClosure::observe(MonthlyClosureObserver::class);
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);

        try {
            $name = Setting::get('app_name');
            if ($name) {
                Config::set('app.name', $name);
            }

            $mailDriver = Setting::get('mail_driver', 'log');
            if ($mailDriver && $mailDriver !== 'log') {
                Config::set('mail.default', $mailDriver);
                Config::set('mail.mailers.smtp.host', Setting::get('mail_host', ''));
                Config::set('mail.mailers.smtp.port', Setting::get('mail_port', '587'));
                Config::set('mail.mailers.smtp.username', Setting::get('mail_username', ''));
                Config::set('mail.mailers.smtp.password', Setting::get('mail_password', ''));
                $enc = Setting::get('mail_encryption', 'tls');
                Config::set('mail.mailers.smtp.encryption', $enc === 'null' ? null : $enc);
                Config::set('mail.from.address', Setting::get('mail_from_address', ''));
                Config::set('mail.from.name', Setting::get('mail_from_name', 'Chronorex Express'));
            }
        } catch (\Throwable $e) {
            // Table might not exist yet (first migration)
        }

        Livewire::component('dashboard', Dashboard::class);
        Livewire::component('expense-form', ExpenseForm::class);
        Livewire::component('expense-list', ExpenseList::class);
        Livewire::component('profile-settings', ProfileSettings::class);
        Livewire::component('employee-form', EmployeeForm::class);
        Livewire::component('employee-list', EmployeeList::class);
        Livewire::component('settings', Settings::class);
        Livewire::component('users', Users::class);
        Livewire::component('roles', Roles::class);
        Livewire::component('categories', Categories::class);
        Livewire::component('audit-logs', AuditLogs::class);
        Livewire::component('email-templates', EmailTemplates::class);
        Livewire::component('treasury-index', TreasuryIndex::class);
        Livewire::component('forgot-password', ForgotPassword::class);
        Livewire::component('reset-password', ResetPassword::class);

        Gate::define('manage-users', function (User $user) {
            return $user->hasPermission('users');
        });

        Gate::define('manage-roles', function (User $user) {
            return $user->hasPermission('roles');
        });

        Gate::define('manage-treasury', function (User $user) {
            return $user->hasPermission('treasury');
        });

        Gate::define('manage-reports', function (User $user) {
            return $user->hasPermission('reports');
        });

        Gate::define('manage-statistics', function (User $user) {
            return $user->hasPermission('statistics');
        });

        Gate::define('manage-employees', function (User $user) {
            return $user->hasPermission('employees');
        });

        Gate::define('manage-email-templates', function (User $user) {
            return $user->hasPermission('email-templates');
        });

        Gate::define('manage-backups', function (User $user) {
            return $user->hasPermission('settings');
        });

        Gate::define('manage-delete-closure', function (User $user) {
            return $user->hasPermission('delete-closure');
        });

        Gate::define('view-audit-logs', function (User $user) {
            return $user->hasPermission('audit-logs');
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->hasPermission('settings');
        });

        Gate::define('manage-categories', function (User $user) {
            return $user->hasPermission('categories');
        });

        Gate::define('login-as', function (User $user) {
            return $user->hasPermission('login-as');
        });

        Notification::extend('whatsapp', function ($app) {
            return $app->make(\App\Domains\Alerts\Channels\WhatsAppChannel::class);
        });

        RateLimiter::for('login', function (Request $request) {
            $key = $request->user()?->id ?: ($request->ip() . '|' . $request->input('email', ''));
            return Limit::perMinute(3)->by($key);
        });

        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip() . '|' . $request->input('email', ''));
        });
    }
}
