<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $newVariables = [
        'expense_created'  => ['date', 'payment_method', 'company_name'],
        'expense_modified' => ['date', 'payment_method', 'company_name'],
        'expense_deleted'  => ['date', 'payment_method', 'company_name'],
        'high_expense'     => ['threshold', 'company_name'],
        'salary_reminder'  => ['company_name'],
        'monthly_closure'  => ['company_name'],
        'daily_report'     => ['company_name'],
        'deficit_increased'=> ['company_name'],
        'deficit_deducted' => ['company_name'],
        'deficit_covered'  => ['company_name'],
    ];

    public function up(): void
    {
        foreach ($this->newVariables as $type => $addVars) {
            $row = DB::table('whatsapp_message_templates')->where('type', $type)->first();
            if (!$row) continue;

            $existing = json_decode($row->variables ?? '[]', true) ?? [];

            // Merge new vars at the end, avoiding duplicates
            foreach ($addVars as $v) {
                if (!in_array($v, $existing, true)) {
                    $existing[] = $v;
                }
            }

            DB::table('whatsapp_message_templates')
                ->where('type', $type)
                ->update(['variables' => json_encode($existing)]);
        }
    }

    public function down(): void
    {
        foreach ($this->newVariables as $type => $removeVars) {
            $row = DB::table('whatsapp_message_templates')->where('type', $type)->first();
            if (!$row) continue;

            $existing = json_decode($row->variables ?? '[]', true) ?? [];
            $existing = array_values(array_diff($existing, $removeVars));

            DB::table('whatsapp_message_templates')
                ->where('type', $type)
                ->update(['variables' => json_encode($existing)]);
        }
    }
};
