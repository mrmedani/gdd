<?php

use App\Domains\Alerts\Commands\CheckBudgets;
use App\Domains\Alerts\Commands\CheckHighExpenses;
use App\Domains\Alerts\Commands\CheckMissingReceipts;
use App\Domains\Alerts\Commands\CleanupAlerts;
use Illuminate\Support\Facades\Schedule;

Schedule::command('alerts:high-expenses')->dailyAt('23:00');
Schedule::command('backup:database')->weeklyOn(1, '01:00');
Schedule::command('alerts:salary-reminders')->monthlyOn(20, '08:00');
Schedule::command('alerts:report', ['daily'])->dailyAt('20:00');
Schedule::command('alerts:report weekly')->weeklyOn(6, '20:00');
Schedule::command('alerts:report monthly')->monthlyOn(1, '08:00');
Schedule::command('alerts:missing-receipts')->dailyAt('09:00');
Schedule::command('alerts:check-budgets')->dailyAt('10:00');
Schedule::command('alerts:cleanup 90')->dailyAt('03:00');
Schedule::command('optimize:clear')->hourly();
