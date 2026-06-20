# PROJECT MAP — Chronorex Express Expense Platform

## [TECH_STACK]
* **Backend Framework**: PHP 8.4 / Laravel 11
* **Frontend Framework**: Livewire v4 / Alpine.js v3
* **Styling**: Tailwind CSS v4 (responsive utility-first styling with dynamic LTR/RTL support)
* **Database**: SQLite (Development & Local), MySQL v8.0 / MariaDB (Production)
* **Export Engines**: Barryvdh Laravel DomPDF v3.1 (PDF Generation), Maatwebsite Excel v3.1 (Excel spreadsheets)
* **Charts**: Chart.js v4 (client-side interactive financial diagrams)

## [SYSTEM_FLOW]
1. **Authentication**: 
   * Guest -> Accesses `/login`.
   * Rate limited at 5 tries/min.
   * Authentication checks database `User` and linked `Role` (`admin` or `accountant`).
   * Success -> Dashboard. Failure -> Custom localized error.
2. **Expense & Audit Trail**:
   * Accountant/Admin inputs expense -> `ExpenseForm` validates data.
   * `ExpenseObserver` intercepts Eloquent events (`created`, `updated`, `deleted`).
   * On writing: Logs detailed changes into `audit_logs` database.
   * On deletion: Automatically purges uploaded receipts (images/PDFs) from `public/receipts`.
3. **High Dépenses Alerts & Database Backups**:
   * Automatic Cron Command `alerts:high-expenses` runs daily:
     * Pulls `high_expense_threshold` from `settings` table (fallback to config).
     * If expenses >= threshold, generates an `Alert` and broadcasts `HighExpenseNotification` to Admins.
   * Automatic Cron Command `backup:database` runs weekly:
     * Backs up database dynamically (copies SQLite file or calls `mysqldump` for MySQL).
     * Retains a maximum of 30 backup instances (rotated automatically).
4. **Multilingual Report Export**:
   * User requests Month/Year PDF or Excel -> `ReportController` handles routing.
   * Validated using `ReportRequest`.
   * Translates payment methods and categories dynamically to the user's active locale (AR/FR/EN) and downloads.

## [ARCHITECTURE]
The system implements a Modular Domain-Driven Design (DDD) to group cohesive business logic:
* **`app/Domains/Alerts`** : Command-line actions for backups and threshold checking, DB alerts model, and mail notifications.
* **`app/Domains/Dashboard`** : Home view statistics calculation and Chart.js aggregation.
* **`app/Domains/Expenses`** : Core CRUD classes, including `Expense`, `ExpenseCategory`, `AuditLog` models and Livewire components (`ExpenseList`, `ExpenseForm`).
* **`app/Domains/Reports`** : Handles export controllers and custom Excel sheets.
* **`app/Domains/Settings`** : System settings table model and Livewire panels for user accounts, system configuration, and audit logs.
* **`app/Domains/Employees` (PROPOSED - PENDING)** : To be developed to manage employees, salaries, advances, and payment history.

## [ORPHANS & PENDING]
* ⚠️ **Employee Management (إدارة الموظفين)**: Need to create models (`Employee`, `SalaryAdvance`, `SalaryPayment`), migrations, and Livewire CRUD views.
* ⚠️ **Salary Reminders (تذكير بالرواتب)**: Need to implement daily reminder task checking if salaries are due for the month.
* ⚠️ **Translation Keys**: Need to add translations for the new employee module into `resources/lang/ar.json` and `resources/lang/fr.json`.
